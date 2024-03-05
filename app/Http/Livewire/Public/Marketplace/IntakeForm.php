<?php

namespace App\Http\Livewire\Public\Marketplace;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IntakeForm extends Component
{
    protected $listeners = ['userLoggedIn'];

    public ?string $type = null;

    public bool $loggedIn = false;
    public bool $showRegisterForm = true;
    public $user;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $website = null;
    public ?string $summary = null;

    public string $summaryColor = 'text-body';
    public int $summaryCharacters = 0;

    public ?string $success = null;

    private function setUser() {
        $user = Auth::user() ?? false;
        if ($user) {
            $this->loggedIn = true;
            $this->user = $user;
            $this->email = $this->user->email;
        } else {
            $this->reset('loggedIn');
            $this->reset('showRegisterForm');
            $this->reset('user');
        }
    }

    public function rules() {
        return [
            'name' => 'required|string|unique:listings',
            'email' => 'required|email',
            'website' => 'required|url',
            'phone' => 'nullable|string',
            'summary' => 'required|string|max:250',
        ];
    }

    public function submit() {
        $this->validate();

        $listing = Listing::create([
            'name' => $this->name,
            'website' => $this->website,
            'emails' => [$this->email],
            'phone' => $this->phone,
            'summary' => $this->summary,
            'type' => $this->type,
        ]);

        $listing->owners()->attach($this->user->id, ['role' => Listing::USER_ROLE_OWNER]);

        $this->success = 'Successfully created the listing for ' . $listing->name;

        $this->redirect($listing->show_url);
    }

    public function updatedSummary() {
        $this->summaryCharacters = strlen($this->summary);

        if ($this->summaryCharacters === 0) {
            $this->summaryColor = 'text-body';
        } elseif($this->summaryCharacters <= 250) {
            $this->summaryColor = 'text-success';
        } else {
            $this->summaryColor = 'text-danger';
        }
    }

    public function userLoggedIn() {
        $this->setUser();
    }

    public function toggleRegisterForm() {
        $this->showRegisterForm = !$this->showRegisterForm;
    }

    public function mount() {
        $this->setUser();
    }

    public function updatedType() {
        $this->setUser();
    }

    public function render()
    {
        return view('livewire.public.marketplace.intake-form', [
            'typeOptions' => Listing::TYPES
        ]);
    }
}
