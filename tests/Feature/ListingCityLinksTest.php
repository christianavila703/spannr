<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Article;
use App\Models\Listing;
use App\Support\ListingCityLinks;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-8. Internal links between marketplace listings and city guides.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ListingCityLinksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        // MySQL compares the type column case-insensitively, so /marketplace/clinic/... finds type "Clinic". Match that.
        DB::statement('create table listings (
            id integer primary key autoincrement, name varchar not null unique, slug varchar unique, type varchar collate nocase not null,
            status varchar not null default "Pending", website varchar, emails text, affiliate_link varchar, phone varchar,
            relatable_type varchar, relatable_id integer, summary varchar, description text, licenses text, certifications text,
            payment_options text, insurance integer, virtual integer, created_at datetime, updated_at datetime)');

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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status');
            $table->mediumText('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('page_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('sources')->nullable();
            $table->longText('sidebar')->nullable();
            $table->json('faq')->nullable();
            $table->timestamps();
        });
        (require base_path('database/migrations/2026_10_08_120000_add_reviewer_fields_to_articles_table.php'))->up();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('byline')->nullable();
            $table->string('email')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('bio')->nullable();
            $table->timestamps();
        });
        Schema::create('article_user', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('listing_user', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable();
            $table->timestamps();
        });
        Schema::create('listing_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->morphs('listable');
            $table->string('label')->nullable();
            $table->timestamps();
        });
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('depth')->default(0);
            $table->timestamps();
        });
        Schema::create('taggables', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->morphs('taggable');
            $table->timestamps();
        });
        Schema::create('tag_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('tag_tag_set', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('tag_set_id');
            $table->timestamps();
        });
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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->uuid('uuid')->nullable();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        // The layout asks Vite for the built assets, which do not exist in a test run.
        $this->withoutVite();
    }

    /** A Public Clinic in Miami, FL unless told otherwise, with its primary address. */
    private function listing(string $name, string $city = 'Miami', string $stateCode = 'FL', string $state = 'Florida', string $type = Listing::TYPE_CLINIC, string $status = Listing::STATUS_PUBLIC, array $address = []): Listing
    {
        $listing = Listing::factory()->create(['name' => $name, 'type' => $type, 'status' => $status]);

        Address::factory()->for($listing, 'addressable')->create(array_merge(
            ['city' => $city, 'state' => $state, 'state_code' => $stateCode],
            $address,
        ));

        return $listing->fresh();
    }

    private function guide(string $slug, string $status = Article::STATUS_PUBLISHED): Article
    {
        return Article::factory()->slug($slug)->create(['status' => $status]);
    }

    /** @return array<int, string> */
    private function names($listings): array
    {
        return $listings->pluck('name')->all();
    }

    private function miamiClinics(): array
    {
        $byName = [];
        foreach (['Golf', 'Alpha', 'Echo', 'Bravo', 'India', 'Charlie', 'Hotel', 'Foxtrot', 'Delta'] as $name) {
            $byName[$name] = $this->listing($name . ' Clinic');
        }

        return $byName;
    }

    /* ------------------------------------------------------------------ next six */

    public function test_next_six_are_the_following_clinics_alphabetically_and_never_the_listing_itself(): void
    {
        $clinics = $this->miamiClinics();

        $this->assertSame(
            ['Golf Clinic', 'Hotel Clinic', 'India Clinic', 'Alpha Clinic', 'Bravo Clinic', 'Charlie Clinic'],
            $this->names(ListingCityLinks::nextInCity($clinics['Foxtrot'])),
            'after Foxtrot, wrapping from India round to the start',
        );
        $this->assertSame(
            ['Bravo Clinic', 'Charlie Clinic', 'Delta Clinic', 'Echo Clinic', 'Foxtrot Clinic', 'Golf Clinic'],
            $this->names(ListingCityLinks::nextInCity($clinics['Alpha'])),
        );
    }

    public function test_the_last_listing_wraps_to_the_start(): void
    {
        $clinics = $this->miamiClinics();

        $this->assertSame(
            ['Alpha Clinic', 'Bravo Clinic', 'Charlie Clinic', 'Delta Clinic', 'Echo Clinic', 'Foxtrot Clinic'],
            $this->names(ListingCityLinks::nextInCity($clinics['India'])),
        );
    }

    public function test_a_city_with_fewer_than_seven_listings_returns_all_the_others(): void
    {
        $a = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic');
        $c = $this->listing('Charlie Clinic');

        $this->assertSame(['Bravo Clinic', 'Charlie Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
        $this->assertSame(['Alpha Clinic', 'Bravo Clinic'], $this->names(ListingCityLinks::nextInCity($c)));
    }

    public function test_a_lone_listing_has_no_next_listings(): void
    {
        $this->assertCount(0, ListingCityLinks::nextInCity($this->listing('Only Clinic')));
    }

    public function test_only_public_listings_appear(): void
    {
        $a = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic', status: Listing::STATUS_PENDING);
        $this->listing('Charlie Clinic');

        $this->assertSame(['Charlie Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
    }

    public function test_only_the_same_city_and_state_appear(): void
    {
        $a = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic', 'Miami', 'OH', 'Ohio');          // same city name, other state
        $this->listing('Charlie Clinic', 'Austin', 'TX', 'Texas');      // other city
        $this->listing('Delta Clinic', 'Miami Beach');                  // a different city
        $this->listing('Echo Clinic');

        $this->assertSame(['Echo Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
    }

    public function test_only_the_same_type_appears(): void
    {
        $clinic = $this->listing('Alpha Clinic');
        $doctor = $this->listing('Dr Bravo', type: Listing::TYPE_DOCTOR);
        $this->listing('Charlie Clinic');
        $this->listing('Dr Delta', type: Listing::TYPE_DOCTOR);

        $this->assertSame(['Charlie Clinic'], $this->names(ListingCityLinks::nextInCity($clinic)));
        $this->assertSame(['Dr Delta'], $this->names(ListingCityLinks::nextInCity($doctor)));
    }

    public function test_only_the_primary_address_counts(): void
    {
        $a = $this->listing('Alpha Clinic');
        $other = $this->listing('Bravo Clinic', 'Austin', 'TX', 'Texas');
        Address::factory()->for($other, 'addressable')->create(['city' => 'Miami', 'state' => 'Florida', 'state_code' => 'FL', 'primary' => 0]);

        $this->assertCount(0, ListingCityLinks::nextInCity($a));
    }

    public function test_coach_and_retreat_listings_get_no_next_listings(): void
    {
        $coach = $this->listing('Alpha Coach', type: Listing::TYPE_COACH);
        $this->listing('Bravo Coach', type: Listing::TYPE_COACH);

        $this->assertCount(0, ListingCityLinks::nextInCity($coach));
    }

    /* ------------------------------------------------------------------ city and state matching */

    public function test_city_matching_ignores_case_spacing_punctuation_and_accents(): void
    {
        $a = $this->listing('Alpha Clinic', 'Miami');
        $this->listing('Bravo Clinic', '  MIAMI ');
        $this->listing('Charlie Clinic', 'miami');

        $this->assertSame(['Bravo Clinic', 'Charlie Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
        $this->assertSame(ListingCityLinks::cityKey('St. Louis'), ListingCityLinks::cityKey('Saint  Louis'));
        $this->assertSame(ListingCityLinks::cityKey("O'Fallon"), ListingCityLinks::cityKey('o-fallon'));
        $this->assertSame(ListingCityLinks::cityKey('Montréal'), ListingCityLinks::cityKey('Montreal'));
    }

    public function test_new_york_and_new_york_city_are_one_city(): void
    {
        $a = $this->listing('Alpha Clinic', 'New York', 'NY', 'New York');
        $this->listing('Bravo Clinic', 'New York City', 'NY', 'New York');
        $this->listing('Charlie Clinic', 'Albany', 'NY', 'New York');

        $this->assertSame(['Bravo Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
        $this->assertSame(ListingCityLinks::cityKey('New York City'), ListingCityLinks::cityKey('new york'));
    }

    public function test_the_state_can_come_from_the_full_name_when_the_code_is_blank(): void
    {
        $a = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic', 'Miami', '', 'Florida');

        $this->assertSame(['Bravo Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
        $this->assertSame('Florida', ListingCityLinks::stateName('fl', null));
        $this->assertSame('Florida', ListingCityLinks::stateName(null, 'florida'));
        $this->assertNull(ListingCityLinks::stateName('', ''));
        $this->assertNull(ListingCityLinks::stateName('ON', 'Ontario'));
    }

    public function test_listings_outside_the_us_are_not_matched(): void
    {
        $a = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic', address: ['country_code' => 'gb', 'country' => 'United Kingdom']);
        $uk = $this->listing('Charlie Clinic', address: ['country_code' => 'gb', 'country' => 'United Kingdom']);

        $this->assertCount(0, ListingCityLinks::nextInCity($a));
        $this->assertNull(ListingCityLinks::locationOf($uk));
    }

    public function test_us_country_code_variants_are_accepted(): void
    {
        $a = $this->listing('Alpha Clinic', address: ['country_code' => 'US']);
        $this->listing('Bravo Clinic', address: ['country_code' => 'usa']);
        $this->listing('Charlie Clinic', address: ['country_code' => 'United States']);

        $this->assertSame(['Bravo Clinic', 'Charlie Clinic'], $this->names(ListingCityLinks::nextInCity($a)));
    }

    /* ------------------------------------------------------------------ the guide for a listing */

    public function test_the_guide_is_found_by_city_and_state_and_only_when_published(): void
    {
        $clinic = $this->listing('Alpha Clinic');

        $this->assertNull(ListingCityLinks::guideFor($clinic), 'no guide yet');

        $this->guide('longevity-doctors-miami-fl', Article::STATUS_DRAFT);
        $this->assertNull(ListingCityLinks::guideFor($clinic), 'a draft guide is not linked');

        Article::where('slug', 'longevity-doctors-miami-fl')->update(['status' => Article::STATUS_PUBLISHED]);
        $this->assertSame('longevity-doctors-miami-fl', ListingCityLinks::guideFor($clinic)->slug);
    }

    public function test_the_guide_must_match_the_state_as_well_as_the_city(): void
    {
        $this->guide('longevity-doctors-miami-oh');
        $this->guide('longevity-doctors-austin-tx');

        $this->assertNull(ListingCityLinks::guideFor($this->listing('Alpha Clinic')));
    }

    public function test_a_guide_for_another_city_is_not_matched(): void
    {
        $this->guide('longevity-doctors-austin');
        $this->guide('longevity-doctors-boston');

        $this->assertNull(ListingCityLinks::guideFor($this->listing('Alpha Clinic')));
    }

    public function test_new_york_listings_find_the_new_york_city_guide(): void
    {
        $this->guide('longevity-doctors-new-york-city-ny');

        $this->assertSame('longevity-doctors-new-york-city-ny', ListingCityLinks::guideFor($this->listing('Alpha Clinic', 'New York', 'NY', 'New York'))->slug);
    }

    /* ------------------------------------------------------------------ listings for a guide */

    public function test_a_guide_lists_every_public_clinic_and_doctor_in_its_city_alphabetically(): void
    {
        $this->listing('Zulu Clinic');
        $this->listing('Alpha Clinic');
        $this->listing('Dr Mike', type: Listing::TYPE_DOCTOR);
        $this->listing('Coach Carl', type: Listing::TYPE_COACH);
        $this->listing('Retreat Rita', type: Listing::TYPE_RETREAT);
        $this->listing('Pending Clinic', status: Listing::STATUS_PENDING);
        $this->listing('Austin Clinic', 'Austin', 'TX', 'Texas');

        $result = ListingCityLinks::forGuide('longevity-doctors-miami-fl');

        $this->assertSame('Miami', $result['city']);
        $this->assertSame(['Alpha Clinic', 'Dr Mike', 'Zulu Clinic'], $this->names($result['listings']));
    }

    public function test_the_national_hub_and_slugs_that_are_not_city_guides_list_nothing(): void
    {
        $this->listing('Alpha Clinic');

        $this->assertCount(0, ListingCityLinks::forGuide('longevity-doctors')['listings']);
        $this->assertCount(0, ListingCityLinks::forGuide('sleep-and-longevity')['listings']);
        $this->assertNull(ListingCityLinks::forGuide('longevity-doctors')['city']);
    }

    /* ------------------------------------------------------------------ the profile page */

    public function test_a_clinic_page_shows_more_clinics_in_the_city_as_links_to_their_profiles(): void
    {
        $clinics = $this->miamiClinics();
        $this->listing('Dr Mike', type: Listing::TYPE_DOCTOR);

        $html = $this->get('/marketplace/clinic/' . $clinics['Foxtrot']->slug)->assertOk()->getContent();

        $this->assertStringContainsString('<h2 class="h4 mb-3">More longevity clinics in Miami</h2>', $html);
        $block = substr($html, strpos($html, 'class="listing-city-links'));
        foreach (['golf-clinic', 'hotel-clinic', 'india-clinic', 'alpha-clinic', 'bravo-clinic', 'charlie-clinic'] as $slug) {
            $this->assertStringContainsString('<a href="' . url('/marketplace/clinic/' . $slug) . '">', $block, $slug);
        }
        $this->assertStringNotContainsString('>Foxtrot Clinic</a>', $block, 'not itself');
        $this->assertStringNotContainsString('>Delta Clinic</a>', $block, 'only six');
        $this->assertStringNotContainsString('Dr Mike', $block, 'only the same type');
        $this->assertSame(6, substr_count($block, '/marketplace/clinic/'));
    }

    public function test_a_doctor_page_says_doctors(): void
    {
        $doctor = $this->listing('Dr Alpha', type: Listing::TYPE_DOCTOR);
        $this->listing('Dr Bravo', type: Listing::TYPE_DOCTOR);

        $html = $this->get('/marketplace/doctor/' . $doctor->slug)->assertOk()->getContent();

        $this->assertStringContainsString('More longevity doctors in Miami</h2>', $html);
        $this->assertStringContainsString('<a href="' . url('/marketplace/doctor/dr-bravo') . '">Dr Bravo</a>', $html);
        $this->assertStringNotContainsString('More longevity clinics', $html);
    }

    public function test_coach_and_retreat_pages_get_no_block(): void
    {
        $coach = $this->listing('Alpha Coach', type: Listing::TYPE_COACH);
        $this->listing('Bravo Coach', type: Listing::TYPE_COACH);
        $retreat = $this->listing('Alpha Retreat', type: Listing::TYPE_RETREAT);

        foreach (['/marketplace/coach/' . $coach->slug, '/marketplace/retreat/' . $retreat->slug] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('listing-city-links', $html, $path);
            $this->assertStringNotContainsString('All longevity doctors by city', $html, $path);
        }
    }

    public function test_the_city_guide_link_shows_only_when_that_guide_is_published(): void
    {
        $clinic = $this->listing('Alpha Clinic');
        $path = '/marketplace/clinic/' . $clinic->slug;

        $this->assertStringNotContainsString('Longevity doctors in Miami', $this->get($path)->assertOk()->getContent(), 'no guide');

        $this->guide('longevity-doctors-miami-fl', Article::STATUS_DRAFT);
        $this->assertStringNotContainsString('Longevity doctors in Miami', $this->get($path)->assertOk()->getContent(), 'draft guide');

        Article::where('slug', 'longevity-doctors-miami-fl')->update(['status' => Article::STATUS_PUBLISHED]);
        $this->assertStringContainsString(
            '<a href="' . url('/articles/longevity-doctors-miami-fl') . '">Longevity doctors in Miami</a>',
            $this->get($path)->assertOk()->getContent(),
        );
    }

    public function test_the_national_hub_link_is_always_there_even_with_no_neighbours_and_no_address(): void
    {
        $alone = $this->listing('Alpha Clinic');
        $noAddress = Listing::factory()->create(['name' => 'Nowhere Clinic']);
        $abroad = $this->listing('Abroad Clinic', 'London', 'LND', 'England', address: ['country_code' => 'gb', 'country' => 'United Kingdom']);

        foreach ([$alone, $noAddress, $abroad] as $listing) {
            $html = $this->get('/marketplace/clinic/' . $listing->slug)->assertOk()->getContent();

            $this->assertStringContainsString('<a href="' . url('/articles/longevity-doctors') . '">All longevity doctors by city</a>', $html, $listing->name);
            $this->assertStringNotContainsString('More longevity clinics in', $html, $listing->name . ': the list is hidden when empty');
        }
    }

    public function test_the_block_uses_neutral_wording(): void
    {
        $clinic = $this->listing('Alpha Clinic');
        $this->listing('Bravo Clinic');
        $this->guide('longevity-doctors-miami-fl');

        $html = $this->get('/marketplace/clinic/' . $clinic->slug)->assertOk()->getContent();
        $block = substr($html, strpos($html, 'class="listing-city-links'));
        $block = substr($block, 0, strpos($block, '</section>'));

        $this->assertDoesNotMatchRegularExpression('/featured|top |best|sponsor|promoted|recommended/i', $block);
        $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', $block), 'no dashes');
    }

    public function test_the_block_adds_at_most_two_queries_to_a_profile_page(): void
    {
        $this->miamiClinics();
        $doctor = $this->listing('Dr Alpha', type: Listing::TYPE_DOCTOR);
        $this->listing('Dr Bravo', type: Listing::TYPE_DOCTOR);
        $coach = $this->listing('Alpha Coach', type: Listing::TYPE_COACH);
        $this->guide('longevity-doctors-miami-fl');

        $count = function (string $path) {
            $queries = 0;
            DB::listen(function () use (&$queries) { $queries++; });
            $this->get($path)->assertOk();

            return $queries;
        };

        // A coach page renders the same details but no block, so the difference is the block.
        $without = $count('/marketplace/coach/' . $coach->slug);
        $with = $count('/marketplace/doctor/' . $doctor->slug);

        $this->assertLessThanOrEqual($without + 2, $with, "profile page {$with} queries, coach page {$without}");
    }

    /* ------------------------------------------------------------------ the city guide article */

    public function test_a_city_guide_ends_with_the_listings_in_that_city_before_the_author_area(): void
    {
        $this->guide('longevity-doctors-miami-fl');
        $this->listing('Zulu Clinic');
        $this->listing('Alpha Clinic');
        $this->listing('Dr Mike', type: Listing::TYPE_DOCTOR);
        $this->listing('Coach Carl', type: Listing::TYPE_COACH);
        $this->listing('Pending Clinic', status: Listing::STATUS_PENDING);
        $this->listing('Austin Clinic', 'Austin', 'TX', 'Texas');

        $html = $this->get('/articles/longevity-doctors-miami-fl')->assertOk()->getContent();

        $this->assertStringContainsString('id="city-guide-listings-title">Clinics and doctors listed in Miami</h2>', $html);
        $section = substr($html, strpos($html, '<section class="city-guide-listings'));
        $section = substr($section, 0, strpos($section, '</section>'));
        $this->assertSame(['Alpha Clinic', 'Dr Mike', 'Zulu Clinic'], array_map('strip_tags', $this->listItems($section)));
        $this->assertStringContainsString('<a href="' . url('/marketplace/clinic/alpha-clinic') . '">Alpha Clinic</a>', $section);
        $this->assertStringContainsString('<a href="' . url('/marketplace/doctor/dr-mike') . '">Dr Mike</a>', $section);
        foreach (['Coach Carl', 'Pending Clinic', 'Austin Clinic'] as $absent) {
            $this->assertStringNotContainsString($absent, $section);
        }
        $this->assertGreaterThan(strpos($html, 'Body text of the article'), strpos($html, 'city-guide-listings'), 'after the body');
        $this->assertLessThan(strpos($html, 'About the Author'), strpos($html, 'city-guide-listings'), 'before the author area');
        $this->assertStringContainsString('class="list-unstyled city-listings-columns mb-0"', $section);
    }

    /** @return array<int, string> */
    private function listItems(string $html): array
    {
        preg_match_all('#<li[^>]*>(.*?)</li>#s', $html, $m);

        return $m[1];
    }

    public function test_a_city_guide_with_no_listings_shows_no_section_and_still_returns_200(): void
    {
        $this->guide('longevity-doctors-miami-fl');
        $this->listing('Austin Clinic', 'Austin', 'TX', 'Texas');

        $response = $this->get('/articles/longevity-doctors-miami-fl');

        $response->assertOk();
        $this->assertStringNotContainsString('city-guide-listings', $response->getContent());
        $this->assertStringNotContainsString('Clinics and doctors listed in', $response->getContent());
    }

    public function test_international_guides_get_no_section_even_if_a_us_town_has_the_same_name(): void
    {
        $this->guide('longevity-doctors-london');
        $this->guide('longevity-doctors-paris');
        $this->listing('Alpha Clinic', 'London', 'KY', 'Kentucky');
        $this->listing('Bravo Clinic', 'Paris', 'TX', 'Texas');

        foreach (['/articles/longevity-doctors-london', '/articles/longevity-doctors-paris'] as $path) {
            $this->assertStringNotContainsString('city-guide-listings', $this->get($path)->assertOk()->getContent(), $path);
        }
    }

    public function test_the_national_hub_and_other_articles_do_not_get_the_section(): void
    {
        $this->guide('longevity-doctors');
        $this->guide('best-longevity-supplements');
        $this->listing('Alpha Clinic');

        foreach (['/articles/longevity-doctors', '/articles/best-longevity-supplements'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('city-guide-listings', $html, $path);
            $this->assertStringNotContainsString('Clinics and doctors listed in', $html, $path);
        }
    }

    public function test_the_new_york_city_guide_lists_new_york_listings(): void
    {
        $this->guide('longevity-doctors-new-york-city-ny');
        $this->listing('Alpha Clinic', 'New York', 'NY', 'New York');
        $this->listing('Bravo Clinic', 'New York City', 'NY', 'New York');

        $html = $this->get('/articles/longevity-doctors-new-york-city-ny')->assertOk()->getContent();

        $this->assertStringContainsString('Clinics and doctors listed in New York City</h2>', $html);
        $this->assertStringContainsString('>Alpha Clinic</a>', $html);
        $this->assertStringContainsString('>Bravo Clinic</a>', $html);
    }

    public function test_the_two_column_css_is_in_the_posts_sass_file_and_scoped(): void
    {
        $scss = file_get_contents(resource_path('sass/_posts.scss'));

        $this->assertMatchesRegularExpression('/\.single-post \.city-listings-columns \{\s*@media \(min-width: \$bp-min-tablet\) \{\s*column-count: 2;/', $scss);
        $this->assertFileDoesNotExist(resource_path('sass/_city-listings.scss'));
    }

    /* ------------------------------------------------------------------ DEV-8b: guides whose slug has no state code */

    public function test_a_stateless_guide_page_lists_the_clinics_and_doctors_in_its_city(): void
    {
        $this->guide('longevity-doctors-miami');
        $this->listing('Zulu Clinic');
        $this->listing('Alpha Clinic');
        $this->listing('Dr Mike', type: Listing::TYPE_DOCTOR);
        $this->listing('Coach Carl', type: Listing::TYPE_COACH);
        $this->listing('Pending Clinic', status: Listing::STATUS_PENDING);
        $this->listing('Austin Clinic', 'Austin', 'TX', 'Texas');

        $html = $this->get('/articles/longevity-doctors-miami')->assertOk()->getContent();

        $this->assertStringContainsString('id="city-guide-listings-title">Clinics and doctors listed in Miami</h2>', $html);
        $section = substr($html, strpos($html, '<section class="city-guide-listings'));
        $section = substr($section, 0, strpos($section, '</section>'));
        $this->assertSame(['Alpha Clinic', 'Dr Mike', 'Zulu Clinic'], array_map('strip_tags', $this->listItems($section)));
        $this->assertLessThan(strpos($html, 'About the Author'), strpos($html, 'city-guide-listings'));
    }

    public function test_a_stateless_guide_matches_the_city_in_any_us_state_and_old_state_coded_guides_are_unchanged(): void
    {
        $this->guide('longevity-doctors-austin');
        $this->listing('Alpha Clinic', 'Austin', 'TX', 'Texas');
        $this->listing('Bravo Clinic', 'Austin', 'MN', 'Minnesota');
        $this->listing('Charlie Clinic', 'Austin', 'TX', 'Texas');

        $this->assertSame(['Alpha Clinic', 'Charlie Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-austin')['listings']));
        // The state-coded guide still means exactly that state.
        $this->assertSame(['Bravo Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-austin-mn')['listings']));
        $this->assertSame(['Alpha Clinic', 'Charlie Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-austin-tx')['listings']));
    }

    public function test_a_city_with_listings_in_two_states_keeps_only_the_state_with_the_most(): void
    {
        $this->listing('Alpha Clinic', 'Springfield', 'IL', 'Illinois');
        $this->listing('Bravo Clinic', 'Springfield', 'MO', 'Missouri');
        $this->listing('Charlie Clinic', 'Springfield', 'MO', 'Missouri');
        $this->listing('Delta Clinic', 'Springfield', 'MO', 'Missouri');
        $this->listing('Echo Clinic', 'Springfield', 'OH', 'Ohio');

        $this->assertSame(['Bravo Clinic', 'Charlie Clinic', 'Delta Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-springfield')['listings']));
    }

    public function test_a_tie_between_states_goes_to_the_alphabetically_first_state(): void
    {
        $this->listing('Alpha Clinic', 'Austin', 'TX', 'Texas');
        $this->listing('Bravo Clinic', 'Austin', 'TX', 'Texas');
        $this->listing('Charlie Clinic', 'Austin', 'MN', 'Minnesota');
        $this->listing('Delta Clinic', 'Austin', 'MN', 'Minnesota');

        $this->assertSame(['Charlie Clinic', 'Delta Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-austin')['listings']));
    }

    public function test_new_york_and_new_york_city_both_match_a_stateless_new_york_guide(): void
    {
        $this->listing('Alpha Clinic', 'New York', 'NY', 'New York');
        $this->listing('Bravo Clinic', 'New York City', 'NY', 'New York');
        $this->listing('Charlie Clinic', 'Albany', 'NY', 'New York');

        foreach (['longevity-doctors-new-york', 'longevity-doctors-new-york-city'] as $slug) {
            $this->assertSame(['Alpha Clinic', 'Bravo Clinic'], $this->names(ListingCityLinks::forGuide($slug)['listings']), $slug);
        }
    }

    public function test_st_and_saint_match_on_a_stateless_guide(): void
    {
        $this->listing('Alpha Clinic', 'St. Louis', 'MO', 'Missouri');
        $this->listing('Bravo Clinic', 'Saint Louis', 'MO', 'Missouri');

        $this->assertSame(['Alpha Clinic', 'Bravo Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-st-louis')['listings']));
        $this->assertSame(['Alpha Clinic', 'Bravo Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-saint-louis')['listings']));
    }

    public function test_multi_word_stateless_guides_work(): void
    {
        $this->listing('Alpha Clinic', 'Los Angeles', 'CA', 'California');
        $this->listing('Bravo Clinic', 'San Francisco', 'CA', 'California');
        $this->listing('Charlie Clinic', 'Las Vegas', 'NV', 'Nevada');

        $this->assertSame(['Alpha Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-los-angeles')['listings']));
        $this->assertSame(['Bravo Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-san-francisco')['listings']));
        $this->assertSame(['Charlie Clinic'], $this->names(ListingCityLinks::forGuide('longevity-doctors-las-vegas')['listings']));
    }

    public function test_international_guides_match_nothing_even_with_a_us_town_of_the_same_name(): void
    {
        $this->listing('Alpha Clinic', 'Paris', 'TX', 'Texas');
        $this->listing('Bravo Clinic', 'London', 'KY', 'Kentucky');

        foreach (['paris', 'london', 'bangkok', 'dubai', 'singapore', 'sydney'] as $city) {
            $result = ListingCityLinks::forGuide('longevity-doctors-' . $city);

            $this->assertCount(0, $result['listings'], $city);
        }
    }

    public function test_a_stateless_guide_with_no_matching_listings_shows_nothing_and_still_returns_200(): void
    {
        $this->guide('longevity-doctors-bangkok');
        $this->guide('longevity-doctors-dubai');
        $this->guide('longevity-doctors-denver');
        $this->listing('Alpha Clinic');

        foreach (['bangkok', 'dubai', 'denver'] as $city) {
            $response = $this->get('/articles/longevity-doctors-' . $city)->assertOk();

            $this->assertStringNotContainsString('city-guide-listings', $response->getContent(), $city);
        }
    }

    public function test_listings_with_no_usable_state_are_ignored_on_a_stateless_guide(): void
    {
        $this->listing('Alpha Clinic', 'Miami', '', '');
        $this->listing('Bravo Clinic', 'Miami', 'ZZ', 'Nowhere');

        $this->assertCount(0, ListingCityLinks::forGuide('longevity-doctors-miami')['listings']);
    }

    public function test_a_listing_links_to_a_stateless_guide_for_its_city(): void
    {
        $clinic = $this->listing('Alpha Clinic', 'Austin', 'TX', 'Texas');
        $this->guide('longevity-doctors-austin');

        $this->assertSame('longevity-doctors-austin', ListingCityLinks::guideFor($clinic)->slug);

        $html = $this->get('/marketplace/clinic/' . $clinic->slug)->assertOk()->getContent();
        $this->assertStringContainsString('<a href="' . url('/articles/longevity-doctors-austin') . '">Longevity doctors in Austin</a>', $html);
    }

    public function test_new_york_listings_link_to_the_stateless_new_york_guide(): void
    {
        $this->guide('longevity-doctors-new-york');

        $this->assertSame('longevity-doctors-new-york', ListingCityLinks::guideFor($this->listing('Alpha Clinic', 'New York', 'NY', 'New York'))->slug);
        $this->assertSame('longevity-doctors-new-york', ListingCityLinks::guideFor($this->listing('Bravo Clinic', 'New York City', 'NY', 'New York'))->slug);
    }

    public function test_when_a_stateless_and_a_state_coded_guide_both_exist_the_stateless_one_wins(): void
    {
        $this->guide('longevity-doctors-new-york-city-ny');
        $this->guide('longevity-doctors-new-york');
        $this->guide('longevity-doctors-austin-tx');
        $this->guide('longevity-doctors-austin');

        $this->assertSame('longevity-doctors-new-york', ListingCityLinks::guideFor($this->listing('Alpha Clinic', 'New York', 'NY', 'New York'))->slug);
        $this->assertSame('longevity-doctors-austin', ListingCityLinks::guideFor($this->listing('Bravo Clinic', 'Austin', 'TX', 'Texas'))->slug);
    }

    public function test_among_several_stateless_guides_for_one_city_the_shortest_slug_wins(): void
    {
        $this->guide('longevity-doctors-new-york-city');
        $this->guide('longevity-doctors-new-york');

        $this->assertSame('longevity-doctors-new-york', ListingCityLinks::guideFor($this->listing('Alpha Clinic', 'New York', 'NY', 'New York'))->slug);
    }

    public function test_the_state_coded_guide_is_still_used_when_there_is_no_stateless_one(): void
    {
        $this->guide('longevity-doctors-miami-fl');

        $this->assertSame('longevity-doctors-miami-fl', ListingCityLinks::guideFor($this->listing('Alpha Clinic'))->slug);
    }

    public function test_a_draft_stateless_guide_is_not_linked_and_does_not_hide_the_coded_one(): void
    {
        $this->guide('longevity-doctors-miami', Article::STATUS_DRAFT);
        $this->guide('longevity-doctors-miami-fl');

        $this->assertSame('longevity-doctors-miami-fl', ListingCityLinks::guideFor($this->listing('Alpha Clinic'))->slug);
    }

    public function test_the_national_hub_is_never_returned_as_a_city_guide(): void
    {
        $this->guide('longevity-doctors');

        $this->assertNull(ListingCityLinks::guideFor($this->listing('Alpha Clinic')));
        $this->assertNull(ListingCityLinks::guideFor($this->listing('Beta Clinic', 'Longevity Doctors', 'FL', 'Florida')));
    }

    public function test_international_guides_are_never_linked_from_a_us_listing_of_the_same_name(): void
    {
        $this->guide('longevity-doctors-paris');
        $paris = $this->listing('Alpha Clinic', 'Paris', 'TX', 'Texas');

        $this->assertNull(ListingCityLinks::guideFor($paris));
        $html = $this->get('/marketplace/clinic/' . $paris->slug)->assertOk()->getContent();
        $this->assertStringNotContainsString('Longevity doctors in Paris', $html);
        $this->assertStringContainsString('All longevity doctors by city', $html);
    }

    public function test_a_stateless_guide_link_adds_no_more_queries_than_a_coded_one(): void
    {
        $this->miamiClinics();
        $doctor = $this->listing('Dr Alpha', type: Listing::TYPE_DOCTOR);
        $this->listing('Dr Bravo', type: Listing::TYPE_DOCTOR);
        $coach = $this->listing('Alpha Coach', type: Listing::TYPE_COACH);
        $this->guide('longevity-doctors-miami');
        $this->guide('longevity-doctors-miami-fl');

        $count = function (string $path) {
            $queries = 0;
            DB::listen(function () use (&$queries) { $queries++; });
            $this->get($path)->assertOk();

            return $queries;
        };

        $without = $count('/marketplace/coach/' . $coach->slug);
        $with = $count('/marketplace/doctor/' . $doctor->slug);

        $this->assertLessThanOrEqual($without + 2, $with, "profile page {$with} queries, coach page {$without}");
        $this->assertStringContainsString('/articles/longevity-doctors-miami"', $this->get('/marketplace/doctor/' . $doctor->slug)->getContent(), 'the stateless guide won');
    }

    public function test_a_stateless_guide_page_stays_within_a_small_number_of_queries(): void
    {
        $this->guide('longevity-doctors-miami');
        $this->miamiClinics();
        $this->guide('sleep-and-longevity');

        $count = function (string $path) {
            $queries = 0;
            DB::listen(function () use (&$queries) { $queries++; });
            $this->get($path)->assertOk();

            return $queries;
        };

        $plain = $count('/articles/sleep-and-longevity');
        $guide = $count('/articles/longevity-doctors-miami');

        $this->assertLessThanOrEqual($plain + 1, $guide, "city guide {$guide} queries, plain article {$plain}");
    }
}
