<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_user', function (Blueprint $table) {
            $table->foreignId('article_id')->references('id')->on('articles')->cascadeOnDelete();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['article_id', 'user_id']);
            $table->timestamps();
        });

        $articles = DB::select('SELECT * FROM articles WHERE user_id IS NOT NULL');

        foreach ($articles as $article) {
            DB::insert('INSERT INTO article_user (article_id, user_id) VALUES (?, ?)', [$article->id, $article->user_id]);
        }

        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('sources')->references('id')->on('users')->nullOnDelete();
        });

        Schema::dropIfExists('article_user');
    }
};
