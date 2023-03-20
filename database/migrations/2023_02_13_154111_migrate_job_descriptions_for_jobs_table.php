<?php

use App\Models\Job;
use Carbon\Carbon;
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
        Job::where('posted_date', '<', Carbon::now()->subMonths(4)->format('Y-m-d'))->chunk(200, function ($jobs) {
            foreach ($jobs as $job) {
                $job->status = Job::STATUS_ARCHIVED;
                $job->exp_date = Carbon::now();
                $job->save();
            }
        });

        Job::whereNull('description')->chunk(200, function ($jobs) {
            foreach ($jobs as $job) {
                $oldDescription = $job->trixRichText->where('field', 'description')->first();

                if ($oldDescription) {
                    $job->description = $oldDescription->content;
                    $job->save();
                }
            }
        });
    }
};
