<?php

namespace App\View\Components\Entities\Data;

use App\Models\Job;
use Illuminate\View\Component;

class JobData extends Component
{
    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|string|\Closure|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.entities.data.job-data');
    }
}
