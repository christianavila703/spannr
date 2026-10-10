<?php

namespace Tests\Feature\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The in-memory SQLite tables the public pages (homepage, articles, glossary, marketplace listings) read.
 * Created per test so no real database is ever touched.
 */
trait BuildsPublicPageTables
{
    private function createTables(): void
    {
        // MySQL compares the type column case-insensitively, so /marketplace/clinic/... finds type "Clinic". Match that.
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
        (require base_path('database/migrations/2026_10_09_100000_add_content_updated_at_to_articles_table.php'))->up();
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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
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
        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('group')->nullable();
            $table->string('status')->nullable();
            $table->string('page_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('link')->nullable();
            $table->string('status')->default('Published');
            $table->longText('seo')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }
}
