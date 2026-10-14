<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Organization;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-17. The "Quick Facts about the Longevity Industry" block on the public Companies page no longer mentions jobs:
 * the jobs pages are retired and 301 to the homepage.
 *
 * In-memory SQLite: no real database is touched.
 */
class CompaniesQuickFactsTest extends TestCase
{
    use BuildsPublicPageTables;

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();

        // The shared tables hold a bare organizations table; the Companies page reads more columns.
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('website')->nullable();
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('founded_date')->nullable();
            $table->boolean('longevity')->nullable();
        });
        Schema::create('location_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->morphs('locatable');
            $table->timestamps();
        });
        // Job rows exist, so the old block would have had a count and company names to print.
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
            $table->date('posted_date');
            $table->timestamps();
        });

        $this->withoutVite();
        $this->seedCompanies();
    }

    /** Three companies (two founded in 2010 or later), a location they share, and a job that would have been counted. */
    private function seedCompanies(): void
    {
        $organizations = [];
        foreach ([['Alpha Bio', 'alpha-bio', '2015'], ['Beta Labs', 'beta-labs', '2021'], ['Gamma Health', 'gamma-health', '2001']] as [$name, $slug, $founded]) {
            $organizations[] = DB::table('organizations')->insertGetId([
                'name' => $name, 'slug' => $slug, 'type' => 'Company', 'founded_date' => $founded, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $location = DB::table('locations')->insertGetId(['name' => 'San Diego, CA', 'created_at' => now(), 'updated_at' => now()]);
        foreach ([$organizations[0], $organizations[1]] as $id) {
            DB::table('location_relationships')->insert([
                'location_id' => $location, 'locatable_type' => Organization::class, 'locatable_id' => $id, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $category = DB::table('job_categories')->insertGetId(['name' => 'Research', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('jobs')->insert([
            'name' => 'Scientist', 'slug' => 'scientist', 'category_id' => $category, 'status' => Job::STATUS_OPEN,
            'owner_type' => Organization::class, 'owner_id' => $organizations[0], 'posted_date' => now()->toDateString(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function page(): \Illuminate\Testing\TestResponse
    {
        return $this->get(route('public.content.organizations.index'));
    }

    public function test_the_companies_page_renders(): void
    {
        $this->page()->assertOk();
    }

    public function test_the_page_no_longer_has_the_jobs_lines(): void
    {
        $html = $this->page()->assertOk()->getContent();

        foreach (['catalogued', 'jobs posted', 'Top 3 companies', 'most jobs'] as $gone) {
            $this->assertStringNotContainsString($gone, $html, $gone);
        }
        $this->assertStringNotContainsString('Scientist', $html, 'no job name is printed');
    }

    public function test_the_other_quick_facts_are_still_there(): void
    {
        $response = $this->page()->assertOk();
        $html = $response->getContent();

        foreach (['Quick Facts about the Longevity Industry', 'were started in 2010 or later', 'Top locations for longevity companies'] as $kept) {
            $this->assertStringContainsString($kept, $html, $kept);
        }

        // The numbers behind them are intact: 2 of 3 companies were founded in 2010 or later (67 percent), and the shared location shows 2.
        $this->assertStringContainsString('<strong class="text-quaternary">3</strong> companies, <strong class="text-quaternary">2</strong> were started in 2010 or later', preg_replace('/\s+/', ' ', $html));
        $this->assertStringContainsString("That's 67% of longevity companies", $html);
        $this->assertMatchesRegularExpression('#<strong class="text-quaternary h6 mb-0 me-2 d-block">2</strong>\s*<span>San Diego, CA</span>#', $html);
    }

    public function test_the_quick_facts_list_has_exactly_two_items_and_no_leftover_markup(): void
    {
        $html = $this->page()->assertOk()->getContent();

        $start = strpos($html, 'Quick Facts about the Longevity Industry');
        $block = substr($html, $start, strpos($html, 'Longevity Companies by Founded Date', $start) - $start);

        $this->assertSame(2, preg_match_all('#<li>#', $block), 'founded date item and top locations item');
        $this->assertDoesNotMatchRegularExpression('#<li>\s*</li>#', $block);
        $this->assertStringNotContainsString('DEV-17', $html, 'the Blade comment is not printed');
    }

    public function test_the_view_data_has_no_jobs_values(): void
    {
        $this->page()->assertOk()
            ->assertViewMissing('jobsCount')
            ->assertViewMissing('jobCategoriesCount')
            ->assertViewMissing('topHiringCompanies')
            ->assertViewHas('totalOrganizations', 3)
            ->assertViewHas('foundedAfterValue', 2)
            ->assertViewHas('topLocations');
    }

    public function test_the_controller_no_longer_queries_the_jobs_tables_for_this_page(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        $this->page()->assertOk();

        $this->assertSame([], preg_grep('/\bfrom "jobs"|\bfrom "job_categories"/', $queries), 'no jobs query on the Companies page');
    }

    public function test_a_company_page_still_loads_its_job_count_for_the_restore_note(): void
    {
        // OrganizationController::show() keeps withCount(['jobs', 'mediaItems']); the DEV-16 restore comment depends on it.
        $controller = file_get_contents(app_path('Http/Controllers/Public/Content/Organizations/OrganizationController.php'));

        $this->assertStringContainsString("->withCount(['jobs', 'mediaItems'])", $controller);
    }
}
