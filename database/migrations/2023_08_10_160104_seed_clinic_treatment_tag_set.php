<?php

use App\Models\Tag;
use App\Models\TagSet;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const TAG_SET = TagSet::TAG_SET_MEDICAL_TREATMENTS;

    public function up()
    {
        $tagSet = TagSet::create([
            'name' => self::TAG_SET,
            'description' => 'Treatment options for clinics'
        ]);

        $tags = [
            'Cold Plunge',
            'Red Light Therapy',
            'Sauna',
        ];

        foreach ($tags as $name) {
            $tag = Tag::where('name', $name)->first();
            $tagSet->tags()->attach($tag->id);
        }
    }

    public function down()
    {
        $tagSet = TagSet::where('name', self::TAG_SET)->first();
        $tagSet->delete();
    }
};
