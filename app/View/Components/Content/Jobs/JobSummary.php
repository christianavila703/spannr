<?php

namespace App\View\Components\Content\Jobs;

use App\Models\Job;
use Illuminate\View\Component;

class JobSummary extends Component
{
    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|string|\Closure|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.content.jobs.job-summary');
    }
}
