<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-16. /jobs and /investors are retired: they 301 to the homepage (routes/web.php, commit 234de2e). A visible link to
 * them lands the visitor on the page they are already on, so none of the public pages may still link to them.
 *
 * In-memory SQLite: no real database is touched.
 */
class RetiredLinksTest extends TestCase
{
    use BuildsPublicPageTables;

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();
        $this->createOrganizationTables();

        $this->withoutVite();
    }

    /** The tables the company profile page and the stock index page read, on top of the shared ones. */
    private function createOrganizationTables(): void
    {
        // The shared tables already hold a bare organizations and locations table; the company page reads more columns.
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('website')->nullable();
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->longText('extended_description')->nullable();
            $table->string('image')->nullable();
            $table->string('founded_date')->nullable();
            $table->boolean('longevity')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
        });
        Schema::create('location_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->morphs('locatable');
            $table->timestamps();
        });
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('symbol')->nullable();
            $table->timestamps();
        });
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('market_snapshot_id')->nullable();
            $table->timestamps();
        });
        Schema::create('market_snapshots', function (Blueprint $table) {
            $table->id();
            $table->dateTime('timestamp');
            $table->string('type')->default('hourly');
            $table->string('status')->nullable();
            $table->decimal('market_cap', 20, 0)->nullable();
            $table->decimal('index_change', 16, 4)->nullable();
            $table->decimal('index_value', 18, 2)->nullable();
            $table->decimal('cumulative_change', 16, 4)->nullable();
            $table->timestamps();
        });
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('media_item_organization', function (Blueprint $table) {
            $table->unsignedBigInteger('media_item_id');
            $table->unsignedBigInteger('organization_id');
            $table->timestamps();
        });
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('type')->default('Full Time');
            $table->unsignedBigInteger('category_id');
            $table->string('status');
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->longText('description')->nullable();
            $table->date('posted_date');
            $table->date('exp_date')->nullable();
            $table->integer('salary')->nullable();
            $table->decimal('hourly', 8, 2)->nullable();
            $table->string('url')->nullable();
            $table->integer('remote')->nullable();
            $table->timestamps();
        });
    }

    /* ------------------------------------------------------------------ helpers */

    /**
     * Every href in the page that points at /jobs or /investors (on any host), with any query or fragment ignored.
     *
     * @return array<int, string>
     */
    private function retiredHrefs(string $html): array
    {
        preg_match_all('/\bhref\s*=\s*["\']([^"\']*)["\']/i', $html, $matches);

        return array_values(array_filter($matches[1], function (string $href) {
            $path = parse_url(html_entity_decode($href), PHP_URL_PATH) ?: '';

            return (bool) preg_match('#^/(jobs|investors)(/|$)#i', $path);
        }));
    }

    private function assertNoRetiredLinks(string $html, string $page): void
    {
        $this->assertSame([], $this->retiredHrefs($html), $page . ' still links to a retired page');
    }

    private function article(): void
    {
        DB::table('articles')->insert([
            'date' => '2026-01-10 09:30:00', 'name' => 'Sleep and Longevity', 'slug' => 'sleep-and-longevity', 'status' => 'publish',
            'excerpt' => 'x', 'meta_description' => 'm', 'content' => '<p>Body</p>', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** The glossary widget in the sidebar shows another entry and fails when there is none. */
    private function glossaryEntries(): void
    {
        foreach ([['NAD', 'nad'], ['Sirtuin', 'sirtuin']] as [$name, $slug]) {
            DB::table('glossary')->insert([
                'name' => $name, 'slug' => $slug, 'group' => substr($name, 0, 1), 'status' => 'publish', 'excerpt' => 'x', 'content' => 'x',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function organizationWithJobs(int $jobs = 1): void
    {
        $this->glossaryEntries();

        $id = DB::table('organizations')->insertGetId([
            'name' => 'Acme Longevity', 'slug' => 'acme-longevity', 'type' => 'Company', 'summary' => 'x', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $category = DB::table('job_categories')->insertGetId(['name' => 'Research', 'created_at' => now(), 'updated_at' => now()]);

        for ($i = 1; $i <= $jobs; $i++) {
            DB::table('jobs')->insert([
                'name' => 'Scientist ' . $i, 'slug' => 'scientist-' . $i, 'category_id' => $category, 'status' => \App\Models\Job::STATUS_OPEN,
                'owner_type' => \App\Models\Organization::class, 'owner_id' => $id, 'posted_date' => now()->toDateString(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    /** The permission package looks these tables up when a gate is checked, even though every check is allowed in these tests. */
    private function createPermissionTables(): void
    {
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
    }

    private function homepage(): string
    {
        DB::table('content')->insert(['name' => 'home', 'slug' => 'home', 'status' => 'Published', 'seo' => '<meta name="description" content="x" />', 'created_at' => now(), 'updated_at' => now()]);

        return $this->get('/')->assertOk()->getContent();
    }

    /* ------------------------------------------------------------------ the pages no longer link to the retired pages */

    public function test_the_homepage_has_no_link_to_jobs_or_investors(): void
    {
        $html = $this->homepage();

        $this->assertNoRetiredLinks($html, 'the homepage');
        $this->assertStringNotContainsString('Longevity Jobs', $html, 'the menu item is gone');
        $this->assertStringContainsString('Companies', $html, 'the rest of the Data menu is still there');
    }

    public function test_an_article_page_has_no_link_to_jobs_or_investors(): void
    {
        $this->article();

        $html = $this->get('/articles/sleep-and-longevity')->assertOk()->getContent();

        $this->assertNoRetiredLinks($html, 'an article page');
    }

    public function test_an_organization_page_has_no_link_to_jobs_or_investors_and_no_view_jobs_button(): void
    {
        $this->organizationWithJobs();

        $html = $this->get('/companies/acme-longevity')->assertOk()->getContent();

        $this->assertNoRetiredLinks($html, 'an organization page');
        $this->assertStringNotContainsString('View Jobs', $html);
        $this->assertStringNotContainsString('Recent Jobs at', $html, 'the jobs block is hidden (second DEV-16 change)');
        $this->assertStringNotContainsString('Scientist 1', $html, 'and so is its list');
    }

    public function test_the_stock_index_page_has_no_link_to_jobs_or_investors_and_keeps_its_other_buttons(): void
    {
        // The page shows the latest hourly snapshot against the previous daily close, so it needs one of each.
        foreach ([['daily', now()->subDay(), 100.00], ['hourly', now(), 105.25]] as [$type, $at, $value]) {
            DB::table('market_snapshots')->insert([
                'timestamp' => $at, 'type' => $type, 'status' => \App\Models\MarketSnapshot::STATUS_COMPLETE, 'market_cap' => 12000000000,
                'index_change' => 0.5, 'index_value' => $value, 'cumulative_change' => 5.25, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $html = $this->get('/index')->assertOk()->getContent();

        $this->assertNoRetiredLinks($html, 'the stock index page');
        $this->assertStringNotContainsString("See Who's Hiring", $html);
        foreach (['View Company Profiles', 'Methodology &amp; FAQ', 'Become an Investor'] as $button) {
            $this->assertStringContainsString($button, $html, $button);
        }
        $this->assertSame(3, substr_count($html, 'class="btn btn-dark py-4"'), 'three buttons are left in the row');
    }

    public function test_the_primary_menu_has_no_investors_item_and_no_orphan_jobs_block_for_visitors(): void
    {
        $menu = view('public.includes.menu-primary')->render();

        $this->assertNoRetiredLinks($menu, 'the menu');
        $this->assertStringNotContainsString('Investors', $menu);
        $this->assertStringNotContainsString('Longevity Jobs', $menu);
        $this->assertStringNotContainsString('Find a job in longevity', $menu);

        // The Data section is still a list with its other four items, and nothing is left over from the removed blocks.
        $this->assertSame(1, preg_match('#id="nav-item-data">\s*<ul>(.*?)</ul>#s', $menu, $data));
        preg_match_all('#<a [^>]*>(.*?)</a>#', $data[1], $labels);
        $this->assertSame(['Companies', 'Glossary', 'Ventures'], $labels[1], 'the Data list keeps its other items and no longer has Stock Index');
        $this->assertDoesNotMatchRegularExpression('#<div class="py-3 border-bottom">\s*</div>#', $menu, 'no empty block');
        $this->assertDoesNotMatchRegularExpression('#<ul[^>]*>\s*</ul>#', $menu, 'no empty list');
    }

    public function test_a_person_who_manages_job_listings_keeps_a_link_to_their_own_page_and_nothing_retired(): void
    {
        $this->createPermissionTables();

        Gate::before(fn () => true);
        $user = new User(['name' => 'Poster', 'email' => 'poster@example.test']);
        $user->id = 1;
        $this->actingAs($user);

        $menu = view('public.includes.menu-primary')->render();

        $this->assertNoRetiredLinks($menu, 'the menu for a job poster');
        $this->assertStringContainsString('<a href="' . url('/user/jobs') . '" class="nav-heading-item btn w-100 text-start h6">My Job Listings</a>', $menu);
        $this->assertStringNotContainsString('List New Job', $menu);
        $this->assertStringNotContainsString('Longevity Jobs', $menu);
    }

    public function test_no_view_still_styles_a_menu_item_as_the_jobs_item(): void
    {
        $views = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));

        foreach ($views as $file) {
            if (str_ends_with($file->getFilename(), '.blade.php')) {
                $this->assertStringNotContainsString("currentNavItem === 'jobs'", file_get_contents($file->getPathname()), $file->getPathname());
            }
        }

        $provider = file_get_contents(app_path('Providers/ViewServiceProvider.php'));
        $this->assertStringNotContainsString("=> 'jobs'", $provider);
        $this->assertStringNotContainsString('public.content.jobs', $provider);
        $this->assertStringNotContainsString('public.content.investors', $provider);
    }

    /* ------------------------------------------------------------------ the company jobs block is hidden */

    public function test_an_organization_with_job_records_shows_no_recent_jobs_block_and_mounts_no_jobs_component(): void
    {
        $this->organizationWithJobs(3);
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        $html = $this->get('/companies/acme-longevity')->assertOk()->getContent();

        $this->assertStringNotContainsString('Recent Jobs at', $html);
        $this->assertStringNotContainsString('organization-jobs', $html, 'the Livewire component is not on the page');
        $this->assertStringNotContainsString('id="organization-jobs"', $html);
        $this->assertSame([], preg_grep('/^select \* from "jobs"/', $queries), 'no query reads the job rows');
        $this->assertStringContainsString('Acme Longevity', $html, 'the rest of the company page still renders');
    }

    public function test_the_company_page_source_keeps_a_note_on_how_to_restore_the_block(): void
    {
        $source = file_get_contents(resource_path('views/public/content/organizations/show.blade.php'));

        $this->assertStringNotContainsString('<h2 class="title">Recent Jobs at', $source);
        $this->assertStringNotContainsString('<livewire:public.organizations.organization-jobs', preg_replace('/\{\{--.*?--\}\}/s', '', $source), 'only inside the comment');
        $this->assertStringContainsString('To restore it', $source);
        $this->assertFileExists(app_path('Http/Livewire/Public/Organizations/OrganizationJobs.php'), 'the component is left in place');
        $this->assertFileExists(resource_path('views/components/content/jobs/job-summary.blade.php'), 'and so is the job summary');
    }

    /* ------------------------------------------------------------------ Stock Index is out of the menus, the page stays */

    /** Every href whose path is /index or under /index/. */
    private function stockIndexHrefs(string $html): array
    {
        preg_match_all('/\bhref\s*=\s*["\']([^"\']*)["\']/i', $html, $matches);

        return array_values(array_filter($matches[1], fn (string $href) => (bool) preg_match('#^/index(/|$)#i', parse_url(html_entity_decode($href), PHP_URL_PATH) ?: '')));
    }

    public function test_the_menu_and_footer_on_the_homepage_have_no_link_to_the_stock_index(): void
    {
        $html = $this->homepage();

        $menuStart = strpos($html, 'id="offcanvas-primary-menu"');
        $menu = substr($html, $menuStart, strpos($html, '<div class="modal fade" id="header-subscribe-modal"', $menuStart) - $menuStart);
        $footer = substr($html, strpos($html, '<footer'), strpos($html, '</footer>') - strpos($html, '<footer'));

        $this->assertStringContainsString('nav-item-data', $menu, 'found the menu');
        $this->assertStringContainsString('public-footer', $footer, 'found the footer');
        $this->assertSame([], $this->stockIndexHrefs($menu), 'menu');
        $this->assertSame([], $this->stockIndexHrefs($footer), 'footer');
        $this->assertStringNotContainsString('Stock Index', $menu);
        $this->assertStringNotContainsString('Stock Index', $footer);
        $this->assertSame([], $this->stockIndexHrefs($html), 'nowhere on the homepage');
    }

    public function test_the_menu_for_a_logged_in_person_has_no_stock_index_link_either(): void
    {
        $this->createPermissionTables();
        Gate::before(fn () => false);
        $this->actingAs((new User(['name' => 'Reader', 'email' => 'reader@example.test']))->forceFill(['id' => 1]));

        $menu = view('public.includes.menu-primary')->render();

        $this->assertSame([], $this->stockIndexHrefs($menu));
        $this->assertStringNotContainsString('Stock Index', $menu);
    }

    public function test_the_stock_index_page_and_its_route_are_still_live(): void
    {
        DB::table('market_snapshots')->insert([
            ['timestamp' => now()->subDay(), 'type' => 'daily', 'status' => \App\Models\MarketSnapshot::STATUS_COMPLETE, 'market_cap' => 12000000000, 'index_change' => 0.5, 'index_value' => 100.00, 'cumulative_change' => 5.25, 'created_at' => now(), 'updated_at' => now()],
            ['timestamp' => now(), 'type' => 'hourly', 'status' => \App\Models\MarketSnapshot::STATUS_COMPLETE, 'market_cap' => 12000000000, 'index_change' => 0.5, 'index_value' => 105.25, 'cumulative_change' => 5.25, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->assertSame('/index', route('public.content.longevity-index', [], false));
        $this->get('/index')->assertOk()->assertSee('View Company Profiles');
        $this->get('/index/about')->assertStatus(301);
    }

    /* ------------------------------------------------------------------ the redirects stay */

    public function test_the_retired_urls_still_301_to_the_homepage(): void
    {
        foreach (['/jobs', '/investors', '/jobs/submit/job', '/jobs/apply', '/jobs/apply/some-job', '/jobs/subscription/confirmed', '/jobs/anything/else', '/investors/charts/x.json', '/investors/some-investor'] as $path) {
            $response = $this->get($path);

            $response->assertStatus(301);
            $this->assertContains($response->headers->get('Location'), ['/', url('/')], $path . ' goes to the homepage');
        }
    }

    public function test_the_route_names_still_resolve_so_existing_route_calls_keep_working(): void
    {
        $this->assertSame('/jobs', route('public.content.jobs.index', [], false));
        $this->assertSame('/investors', route('public.content.investors.index', [], false));
        $this->assertSame('/jobs/submit/job', route('public.content.jobs.submit', [], false));
        $this->assertSame('/jobs/apply/x', route('public.content.jobs.apply', 'x', false));
        $this->assertSame('/jobs/subscription/confirmed', route('public.content.jobs.subscription.confirmed', [], false));
    }
}
