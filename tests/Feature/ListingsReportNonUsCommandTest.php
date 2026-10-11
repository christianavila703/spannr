<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Listing;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * DEV-15. listings:report-non-us is strictly read-only: it lists, it never changes a row.
 * In-memory SQLite and faked storage: no real database or file is touched.
 */
class ListingsReportNonUsCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        DB::statement('create table listings (
            id integer primary key autoincrement, name varchar not null unique, slug varchar unique, type varchar collate nocase not null,
            status varchar not null default "Pending", website varchar, emails text, affiliate_link varchar, phone varchar,
            relatable_type varchar, relatable_id integer, summary varchar, description text, licenses text, certifications text,
            payment_options text, insurance integer, virtual integer, latitude varchar, longitude varchar, created_at datetime, updated_at datetime)');
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('state_code');
            $table->string('country');
            $table->string('country_code');
            $table->string('postal_code')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->morphs('addressable');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->boolean('primary')->default(0);
            $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // Creating a listing also creates its (empty) SEO record.
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->longText('description')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('robots')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });

        Storage::fake('local');
    }

    private function listing(string $name, ?array $address, string $type = Listing::TYPE_CLINIC, string $status = Listing::STATUS_PUBLIC, string $created = '2026-01-01 00:00:00'): Listing
    {
        $listing = Listing::factory()->create(['name' => $name, 'type' => $type, 'status' => $status, 'created_at' => $created, 'updated_at' => $created]);

        if ($address !== null) {
            Address::factory()->for($listing, 'addressable')->create($address + ['name' => 'Somewhere']);
        }

        return $listing;
    }

    private const FRANCE = ['city' => 'Paris', 'state' => 'Ile-de-France', 'state_code' => 'IDF', 'country' => 'France', 'country_code' => 'fr', 'postal_code' => '75008'];
    private const US = ['city' => 'Austin', 'state' => 'Texas', 'state_code' => 'TX', 'country' => 'USA', 'country_code' => 'us', 'postal_code' => '78701'];

    /** A snapshot of every listing and address row, to prove nothing changed. */
    private function snapshot(): array
    {
        return [DB::table('listings')->orderBy('id')->get()->all(), DB::table('addresses')->orderBy('id')->get()->all()];
    }

    public function test_it_lists_a_french_listing_and_not_a_us_one(): void
    {
        $this->listing('Clinique de Paris', self::FRANCE);
        $this->listing('Austin Longevity', self::US);

        $exit = \Illuminate\Support\Facades\Artisan::call('listings:report-non-us');
        $output = \Illuminate\Support\Facades\Artisan::output();

        $this->assertSame(0, $exit);
        $this->assertStringContainsString('Clinique de Paris', $output);
        $this->assertStringContainsString(url('/marketplace/clinic/clinique-de-paris'), $output);
        $this->assertStringContainsString('1 listing(s) with a non-US address (1 in France).', $output);
        $this->assertStringContainsString('Nothing was changed.', $output);
        $this->assertStringNotContainsString('Austin Longevity', $output);
    }

    public function test_the_table_shows_id_name_type_status_city_country_and_url(): void
    {
        $french = $this->listing('Dr Marie Curie', self::FRANCE, Listing::TYPE_DOCTOR, Listing::STATUS_PENDING);

        \Illuminate\Support\Facades\Artisan::call('listings:report-non-us');
        $output = \Illuminate\Support\Facades\Artisan::output();

        $this->assertMatchesRegularExpression(
            '/\|\s*' . $french->id . '\s*\|\s*Dr Marie Curie\s*\|\s*Doctor\s*\|\s*Pending\s*\|\s*Paris\s*\|\s*France\s*\|\s*' . preg_quote(url('/marketplace/doctor/dr-marie-curie'), '/') . '\s*\|/',
            $output,
        );
    }

    public function test_other_non_us_countries_and_spelling_variants_are_found_and_us_variants_are_not(): void
    {
        $this->listing('Maple Clinic', ['city' => 'Toronto', 'state' => 'Ontario', 'state_code' => 'ON', 'country' => 'Canada', 'country_code' => 'ca']);
        $this->listing('Code Only Clinic', ['country' => '', 'country_code' => 'FR'] + self::FRANCE);
        $this->listing('Accent Clinic', ['country' => 'Fran' . "\u{00E7}" . 'e', 'country_code' => ''] + self::FRANCE);
        $this->listing('Long Name Clinic', ['country' => 'United States of America', 'country_code' => ''] + self::US);
        $this->listing('Code Variant Clinic', ['country' => '', 'country_code' => 'USA'] + self::US);
        $this->listing('Old Style Clinic', ['country' => 'United States', 'country_code' => 'us'] + self::US);

        \Illuminate\Support\Facades\Artisan::call('listings:report-non-us');
        $output = \Illuminate\Support\Facades\Artisan::output();

        foreach (['Maple Clinic', 'Code Only Clinic', 'Accent Clinic'] as $found) {
            $this->assertStringContainsString($found, $output, $found);
        }
        foreach (['Long Name Clinic', 'Code Variant Clinic', 'Old Style Clinic'] as $notFound) {
            $this->assertStringNotContainsString($notFound, $output, $notFound);
        }
        $this->assertStringContainsString('3 listing(s) with a non-US address (2 in France).', $output);
    }

    public function test_the_primary_address_decides_and_a_listing_with_no_address_is_counted_but_not_listed(): void
    {
        // Primary in the US, a secondary location in France: the listing is a US listing.
        $us = $this->listing('Two Sites Clinic', self::US);
        Address::factory()->for($us, 'addressable')->create(['primary' => 0, 'name' => 'Branch'] + self::FRANCE);
        $this->listing('Nowhere Clinic', null);
        $this->listing('No Country Clinic', ['country' => '', 'country_code' => ''] + self::US);

        $this->artisan('listings:report-non-us')
            ->expectsOutputToContain('No listing with a non-US address was found.')
            ->expectsOutputToContain('2 listing(s) have no address or no country and could not be checked.')
            ->assertExitCode(0);
    }

    public function test_the_list_is_newest_first_with_the_id_as_the_tiebreaker(): void
    {
        $this->listing('Old Paris', self::FRANCE, created: '2025-01-01 00:00:00');
        $this->listing('New Paris', self::FRANCE, created: '2026-06-01 00:00:00');
        $this->listing('Same Day A', self::FRANCE, created: '2026-03-01 00:00:00');
        $this->listing('Same Day B', self::FRANCE, created: '2026-03-01 00:00:00');

        \Illuminate\Support\Facades\Artisan::call('listings:report-non-us');
        $output = \Illuminate\Support\Facades\Artisan::output();

        $positions = array_map(fn ($name) => strpos($output, $name), ['New Paris', 'Same Day B', 'Same Day A', 'Old Paris']);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions, 'New Paris, then Same Day B (higher id), Same Day A, then Old Paris');
    }

    public function test_it_changes_no_row_and_leaves_every_listing_visible(): void
    {
        $this->listing('Clinique de Paris', self::FRANCE);
        $this->listing('Pending Paris', self::FRANCE, status: Listing::STATUS_PENDING);
        $this->listing('Austin Longevity', self::US);
        $this->listing('Nowhere Clinic', null);
        $before = $this->snapshot();

        $this->artisan('listings:report-non-us')->assertExitCode(0);
        $this->artisan('listings:report-non-us', ['--csv' => 'reports/non-us-listings.csv'])->assertExitCode(0);

        $this->assertEquals($before, $this->snapshot(), 'not one column of one row changed');
        $this->assertSame(['Public', 'Pending', 'Public', 'Public'], array_map(fn ($row) => $row->status, $before[0]), 'statuses are as they were');
        $this->assertSame(4, DB::table('listings')->count(), 'nothing deleted');
    }

    public function test_it_writes_a_csv_only_when_asked(): void
    {
        $this->listing('Clinique de Paris', self::FRANCE);
        $this->listing('Austin Longevity', self::US);

        $this->artisan('listings:report-non-us')->assertExitCode(0);
        $this->assertSame([], Storage::disk('local')->allFiles(), 'no file without --csv');

        $this->artisan('listings:report-non-us', ['--csv' => 'reports/non-us-listings.csv'])
            ->expectsOutputToContain('CSV written to storage/app/reports/non-us-listings.csv')
            ->assertExitCode(0);

        Storage::disk('local')->assertExists('reports/non-us-listings.csv');
        $rows = array_map('str_getcsv', explode("\n", trim(Storage::disk('local')->get('reports/non-us-listings.csv'))));
        $this->assertSame(['id', 'name', 'type', 'status', 'city', 'country', 'profile_url', 'created_at'], $rows[0]);
        $this->assertCount(2, $rows, 'the header and the one French listing');
        $this->assertSame(['Clinique de Paris', 'Clinic', 'Public', 'Paris', 'France', url('/marketplace/clinic/clinique-de-paris')], array_slice($rows[1], 1, 6));
    }

    public function test_a_spreadsheet_formula_in_a_name_is_written_as_text(): void
    {
        $this->listing('=HYPERLINK("http://evil.example")', self::FRANCE);

        $this->artisan('listings:report-non-us', ['--csv' => 'out.csv'])->assertExitCode(0);

        $csv = Storage::disk('local')->get('out.csv');
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringNotContainsString(',=HYPERLINK', $csv);
    }

    public function test_an_unsafe_csv_path_is_refused_and_nothing_is_written(): void
    {
        $this->listing('Clinique de Paris', self::FRANCE);

        foreach (['../outside.csv', '/etc/report.csv', 'report.txt', 'a\\b.csv'] as $path) {
            $this->artisan('listings:report-non-us', ['--csv' => $path])
                ->expectsOutputToContain('The --csv path must be a relative .csv path under storage/app')
                ->assertExitCode(1);
        }

        $this->assertSame([], Storage::disk('local')->allFiles());
    }
}
