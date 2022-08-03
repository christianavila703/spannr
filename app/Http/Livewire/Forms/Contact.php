<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormEntry;
use Livewire\Component;

class Contact extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $message;

    public $showSuccess = false;
    public $successClass = 'lead text-center text-quaternary';

    protected array $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'message' => 'required',
    ];

    public function submit() {

        $validated = $this->validate();

        FormEntry::create([
            'form' => FormEntry::FORM_CONTACT,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'fields' => json_encode($validated),
        ]);

        $this->showSuccess = true;
    }

    public function render()
    {
        return view('livewire.forms.contact');
    }
}
