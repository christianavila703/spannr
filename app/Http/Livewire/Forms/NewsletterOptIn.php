<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormEntry;
use Livewire\Component;

class NewsletterOptIn extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $url;
    public $ip;
    public $showNameFields = false;

    public $showSuccess = false;
    public $formType = 'vertical';
    public $formClass = '';
    public $successClass = 'lead text-tertiary fw-bold';

    protected array $rules = [
        'first_name' => 'nullable|string',
        'last_name' => 'nullable|string',
        'email' => 'required|email',
        'url' => 'nullable',
        'ip' => 'nullable',
    ];

    public function submit() {

        $validated = $this->validate();

        FormEntry::create([
            'form' => FormEntry::FORM_NEWSLETTER_SIDEBAR,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
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
        return view('livewire.forms.newsletter-opt-in');
    }
}
