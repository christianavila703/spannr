<?php

use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const TAG_NAME = Tag::TAG_LANGUAGES;

    public function up()
    {
        $children = [
            'English',
            'Mandarin',
            'Hindi',
            'Spanish',
            'French',
            'Arabic',
            'Bengali',
            'Russian',
            'Portuguese',
            'Urdu',
            'Indonesian',
            'German',
            'Japanese',
        ];

        $parentTag = Tag::create([
            'name' => self::TAG_NAME
        ]);

        foreach ($children as $child) {
            Tag::create([
                'name' => $child,
                'parent_id' => $parentTag->id,
                'depth' => 1
            ]);
        }
    }

    public function down()
    {
        $tag = Tag::where('name', self::TAG_NAME)->first();
        Tag::where('parent_id', $tag->id)->delete();
        $tag->delete();
    }
};
