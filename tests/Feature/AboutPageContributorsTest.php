<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-18. The About page leaves out the Contributors section when there are no contributors, and the Admin menu's
 * Backups link points at the real backup panel path (/backup, from config/laravel_backup_panel.php).
 *
 * In-memory SQLite: no real database is touched.
 */
class AboutPageContributorsTest extends TestCase
{
    use BuildsPublicPageTables;

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();

        // The shared users table has no sort column, and the About page orders by it.
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable();
        });

        // The permission package's tables (roles are how a person is on the team or a contributor, and how a gate is checked).
        foreach (['permissions', 'roles'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
            });
        }
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        $this->withoutVite();
    }

    /** A person with the given role (Role::ROLE_SPANNR_TEAM, Role::ROLE_SPANNR_CONTRIBUTOR, ...). */
    private function person(string $name, string $role, ?string $title = null): int
    {
        $id = DB::table('users')->insertGetId([
            'name' => $name, 'email' => str($name)->slug() . '@example.test', 'slug' => str($name)->slug()->toString(), 'title' => $title,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $roleId = DB::table('roles')->where('name', $role)->value('id')
            ?? DB::table('roles')->insertGetId(['name' => $role, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);

        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $id]);

        return $id;
    }

    /* ------------------------------------------------------------------ the About page */

    public function test_with_a_team_member_and_no_contributors_the_section_is_left_out(): void
    {
        $this->person('Tessa Team', Role::ROLE_SPANNR_TEAM, 'Founder');

        $html = $this->get('/about')->assertOk()->getContent();

        $this->assertStringContainsString('Our Team', $html);
        $this->assertStringContainsString('Tessa Team', $html);
        $this->assertStringContainsString('id="team"', $html);
        $this->assertStringNotContainsString('id="contributors"', $html);
        $this->assertStringNotContainsString('Contributors', $html);
        $this->assertStringNotContainsString('DEV-18', $html, 'the Blade comment is not printed');
    }

    public function test_with_no_team_and_no_contributors_the_page_still_renders_without_the_section(): void
    {
        $html = $this->get('/about')->assertOk()->getContent();

        $this->assertStringContainsString('Our Team', $html, 'the Our Team block is untouched');
        $this->assertStringNotContainsString('Contributors', $html);
    }

    public function test_with_a_contributor_the_section_shows_with_that_person(): void
    {
        $this->person('Tessa Team', Role::ROLE_SPANNR_TEAM);
        $this->person('Connor Contributor', Role::ROLE_SPANNR_CONTRIBUTOR, 'Guest Writer');

        $html = $this->get('/about')->assertOk()->getContent();

        $this->assertStringContainsString('id="contributors"', $html);
        $this->assertStringContainsString('<h2 class="h1">Contributors</h2>', $html);
        $this->assertStringContainsString('Connor Contributor', $html);
        $this->assertStringContainsString('Guest Writer', $html);

        // The contributor is in the Contributors section, not in Our Team, and the team member is not in Contributors.
        $contributors = substr($html, strpos($html, 'id="contributors"'));
        $team = substr($html, strpos($html, 'id="team"'), strpos($html, 'id="contributors"') - strpos($html, 'id="team"'));
        $this->assertStringContainsString('Connor Contributor', $contributors);
        $this->assertStringNotContainsString('Tessa Team', $contributors);
        $this->assertStringNotContainsString('Connor Contributor', $team);
        $this->assertStringContainsString('Tessa Team', $team);
    }

    public function test_contributors_only_with_no_team_still_shows_the_section(): void
    {
        $this->person('Connor Contributor', Role::ROLE_SPANNR_CONTRIBUTOR);

        $html = $this->get('/about')->assertOk()->getContent();

        $this->assertStringContainsString('id="contributors"', $html);
        $this->assertStringContainsString('Connor Contributor', $html);
    }

    public function test_the_about_view_guards_the_whole_section_with_a_not_empty_check(): void
    {
        $source = file_get_contents(resource_path('views/public/content/about/index.blade.php'));

        $this->assertStringContainsString('@if ($contributors->isNotEmpty())', $source);
        $this->assertSame(0, preg_match('/\$contributors->isEmpty\(\)/', $source));
        $this->assertSame(1, substr_count($source, 'id="contributors"'), 'one section, nothing else points at it');
    }

    /* ------------------------------------------------------------------ the Admin menu */

    private function adminLayoutHtml(): string
    {
        // Only "admin control" is allowed: the search box and the other menus (each its own permission) stay out of this page.
        Gate::before(fn ($user, $ability) => $ability === 'admin control' ? true : false);

        $id = DB::table('users')->insertGetId(['name' => 'Super Person', 'email' => 'super@example.test', 'created_at' => now(), 'updated_at' => now()]);
        $roleId = DB::table('roles')->insertGetId(['name' => Role::ROLE_SUPER_ADMIN, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $id]);
        $this->actingAs(User::find($id));
        $this->assertSame('Super Person', Auth::user()->name);

        // A bare page that uses the admin layout.
        return Blade::render('@extends("layouts.app") @section("content") <p>Admin page</p> @endsection');
    }

    public function test_the_admin_dropdown_links_backups_to_the_real_panel_path(): void
    {
        $html = $this->adminLayoutHtml();

        $this->assertStringContainsString('Admin page', $html, 'the layout rendered');
        $this->assertStringContainsString('navbar-dropdown-admin', $html, 'the Admin dropdown is there for a Super Admin');
        $this->assertStringContainsString('<a href="/backup" class="dropdown-item">Backups</a>', $html);
        $this->assertStringNotContainsString('/admin/backup', $html);
        $this->assertStringContainsString('Activity Log', $html, 'the other items are untouched');
        $this->assertStringContainsString('<a href="/log-viewer" class="dropdown-item">Logs</a>', $html);
    }

    public function test_the_backups_link_matches_the_backup_panel_route_and_config(): void
    {
        $this->assertSame('backup', config('laravel_backup_panel.path'));
        $this->assertSame('/backup', route('laravel-backup-panel.index', [], false));

        // There is no /admin/backup route: matching it is a 404, so the old link could never have worked.
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        app('router')->getRoutes()->match(\Illuminate\Http\Request::create('/admin/backup', 'GET'));
    }

    public function test_no_view_still_links_to_admin_backup(): void
    {
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (str_ends_with($file->getFilename(), '.blade.php')) {
                $this->assertStringNotContainsString('/admin/backup', file_get_contents($file->getPathname()), $file->getPathname());
            }
        }
    }
}
