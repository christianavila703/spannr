<?php

use App\Models\JobCategory;
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
       JobCategory::updateOrCreate([
            'name' => JobCategory::CATEGORY_OTHER
        ]);
    }
};
