<?php

namespace App\Http\Livewire\Admin\Recruiting;

use App\Models\JobApplication;
use Livewire\Component;

class EditJobApplication extends Component
{
    public JobApplication $jobApplication;

    protected $rules = [
        'jobApplication.status' => 'required',
    ];

    protected $listeners = ['updateSelectedJobApp'];

    public function updateSelectedJobApp($id) {
        $this->jobApplication = JobApplication::find($id);
    }

    public function save()
    {
        $this->validate();
        $this->jobApplication->save();
        $this->dispatchBrowserEvent('closeEditJobAppModal');
    }

    public function render()
    {
        return view('livewire.admin.recruiting.edit-job-application', [
            'statusOptions' => JobApplication::STATUSES
        ]);
    }
}
