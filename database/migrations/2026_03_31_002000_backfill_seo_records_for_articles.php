<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Article::query()->select('id')->chunkById(100, function ($articles): void {
            foreach ($articles as $article) {
                if (! $article->seo()->exists()) {
                    $article->seo()->create([]);
                }
            }
        });
    }

    public function down(): void
    {
        //
    }
};
