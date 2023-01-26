<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormEntry;
use Livewire\Component;

class ReportOptIn extends Component
{
    public $email;
    public $url;
    public $ip;

    public $showSuccess = false;
    public $successClass = 'h5 text-quaternary';

    protected array $rules = [
        'email' => 'required|email',
        'url' => 'nullable',
        'ip' => 'nullable',
    ];

    public function submit() {

        $validated = $this->validate();

        FormEntry::create([
            'form' => FormEntry::FORM_REPORT_LONGEVITY_2022,
            'email' => $validated['email'],
            'url' => $validated['url'],
            'fields' => json_encode($validated),
        ]);

        $this->showSuccess = true;
    }

    public function mount()
    {
        $this->url = url()->current();
        $this->ip = request()->ip();
    }

    public function render()
    {
        return view('livewire.forms.report-opt-in');
    }
}
