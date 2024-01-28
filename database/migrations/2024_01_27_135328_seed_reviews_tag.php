<?php

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        $parent = Tag::where('name', 'Article Categories')->firstOrFail();

        $tag = Tag::create([
            'name' => Article::TAG_REVIEWS,
            'parent_id' => $parent->id,
            'depth' => 1
        ]);

        $articleSlugs = [
            'best-sauna-steam-room-infrared',
            'best-red-light-therapy-devices',
            'best-cold-plunge-longevity-comparing-ice-baths',
            'top-epigenetic-age-tests'
        ];

        $articleIds = Article::whereIn('slug', $articleSlugs)->pluck('id')->toArray();

        $tag->articles()->sync($articleIds);
    }

    public function down() {
        Tag::where('name', Article::TAG_REVIEWS)->delete();
    }
};
