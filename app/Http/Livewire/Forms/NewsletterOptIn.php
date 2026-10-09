<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormEntry;
use Illuminate\Support\Str;
use Livewire\Component;

class NewsletterOptIn extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $url;
    public $ip;
    public $showNameFields = false;
    public $uniquePrefix;

    public $showSuccess = false;
    public $form = FormEntry::FORM_NEWSLETTER_SIDEBAR;
    public $formClass = '';
    public $successClass = 'lead text-tertiary fw-bold';

    // Optional wording. The defaults are the long-standing text, so a place that passes nothing is unchanged.
    public $buttonText = 'Sign Up';
    public $emailPlaceholder = 'Email';
    public $note = ''; // small line between the email field and the button; nothing is printed when empty

    public function mount()
    {
        $this->url = url()->current();
        $this->ip = request()->ip();

        $this->uniquePrefix = ($this->uniquePrefix ?? Str::random(5)) . '-';
    }

    protected array $rules = [
        'first_name' => 'nullable|string',
        'last_name' => 'nullable|string',
        'email' => 'required|email',
        'url' => 'nullable',
        'ip' => 'nullable',
    ];

    public function submit()
    {

        $validated = $this->validate();

        $validated['ip'] = $this->ip;
        $validated['url'] = $this->url;

        FormEntry::create([
            'form' => $this->form,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'url' => $validated['url'],
            'fields' => json_encode($validated),
        ]);

        $this->showSuccess = true;
    }

    public function render()
    {
        return view('livewire.forms.newsletter-opt-in');
    }
}
