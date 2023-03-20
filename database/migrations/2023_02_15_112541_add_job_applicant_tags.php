<?php

use App\Models\Tag;
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
        $peopleTag = Tag::updateOrCreate([
            'name' => Tag::TAG_PEOPLE
        ]);

        Tag::updateOrCreate([
            'name' => Tag::TAG_JOB_APPLICANT,
            'parent_id' => $peopleTag->id
        ]);

        Tag::updateOrCreate([
            'name' => Tag::TAG_HIRING_MANAGER,
            'parent_id' => $peopleTag->id
        ]);
    }
};
