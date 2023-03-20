<?php

namespace App\View\Components\Admin\Jobs;

use App\Models\Job;
use Illuminate\View\Component;

class TableRowData extends Component
{
    public Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|\Closure|string|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.admin.jobs.table-row-data');
    }
}
