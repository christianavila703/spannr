<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEV-7. Optional "reviewed by" details shown on the article page. Additive and nullable only:
 * existing rows are not touched, and nothing is shown publicly unless an editor fills these in.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_title')->nullable();
            $table->date('reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['reviewer_name', 'reviewer_title', 'reviewed_at']);
        });
    }
};
