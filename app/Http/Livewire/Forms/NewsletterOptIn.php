<?php

namespace App\Http\Livewire\Forms;

use App\Models\FormEntry;
use App\Support\NewsletterSignupContext;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
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

    // Where this block sits on the site (one of NewsletterSignupContext::POSITIONS). Anything else is recorded as 'other'.
    public string $position = 'unknown';

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
        // The position is a public property, so the browser can change it: it is checked against the list again here.
        $validated['position'] = NewsletterSignupContext::normalizePosition($this->position);

        FormEntry::create([
            'form' => $this->form,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'url' => $validated['url'],
            'fields' => json_encode($validated),
        ]);

        $this->showSuccess = true;

        $this->rememberSignupContext();
        $this->announceSignup();
    }

    /**
     * Tell the browser a signup was submitted, so the page can send the GA4 newsletter_signup event right now (the
     * confirm link in the email is opened later, often elsewhere). Only called after a successful signup. Tracking must
     * never be able to break a signup, so a problem here is logged and ignored.
     */
    private function announceSignup(): void
    {
        try {
            $this->dispatchBrowserEvent('newsletter-signup', ['position' => NewsletterSignupContext::normalizePosition($this->position)]);
        } catch (Throwable $exception) {
            Log::warning('Newsletter signup event not sent to the browser (' . class_basename($exception) . ')');
        }
    }

    /**
     * Keep where the signup happened until /confirmed, so GA4 can count it by placement. Tracking must never be able to
     * break a signup, so a problem here is logged and ignored.
     */
    private function rememberSignupContext(): void
    {
        try {
            NewsletterSignupContext::remember($this->position, $this->url, request()->headers->get('referer'));
        } catch (Throwable $exception) {
            Log::warning('Newsletter signup context not kept (' . class_basename($exception) . ')');
        }
    }

    public function render()
    {
        return view('livewire.forms.newsletter-opt-in');
    }
}
