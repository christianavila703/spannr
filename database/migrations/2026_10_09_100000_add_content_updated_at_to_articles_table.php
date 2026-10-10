<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEV-5. A hand-set "last updated" date for articles. Additive and nullable: existing rows are not touched.
 * It is the only source of the Article JSON-LD dateModified and of the "Updated:" line in the author box;
 * updated_at changes on any save (typo fixes, FAQ edits) and is never used for either.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->date('content_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('content_updated_at');
        });
    }
};
