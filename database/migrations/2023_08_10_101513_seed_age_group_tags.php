<?php

use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const TAG_NAME = Tag::TAG_AGE_GROUPS;

    public function up()
    {
        $children = [
            'Children',
            'Teenagers',
            'Adults 18-34',
            'Adults 35-44',
            'Adults 45-54',
            'Adults 55-64',
            'Adults 65+'
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
