<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $parent = \App\Models\Tag::create([
            'name' => 'Article Categories'
        ]);

        $children = [
            'Business',
            'Events',
            \App\Models\Article::TAG_INVESTING,
            'Learn',
            'Lifestyle',
            'Research',
            \App\Models\Article::TAG_TOP_INSIGHTS,
        ];

        foreach ($children as $child) {
            \App\Models\Tag::create([
                'name' => $child,
                'parent_id' => $parent->id
            ]);
        }
    }
};
