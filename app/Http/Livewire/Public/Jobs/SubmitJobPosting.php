<?php

namespace App\Http\Livewire\Public\Jobs;

use App\Models\FormEntry;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;

class SubmitJobPosting extends Component
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $name;
    public $description;
    public $qualifications;
    public $salary_min;
    public $salary_max;
    public $category;
    public $category_other;
    public $monthly;
    public $recruiter;
    public $remote;
    public $type;
    public $organization;
    public $user;

    public $locations = [];

    public $categoryOtherId;

    public $error;
    public $success;

    protected $listeners = ['setOrganization', 'updateDescription', 'updateQualifications', 'updateJobLocation', 'clearLocations'];

    public function mount() {
        $this->category = 'placeholder';

        if (Auth::user()) {
            $this->user = Auth::user();
            $this->first_name = $this->user->name;
            $this->last_name = $this->user->last_name;
            $this->email = $this->user->email;
        } else {
            $this->user = null;
        }

        $categoryOther = JobCategory::where('name', JobCategory::CATEGORY_OTHER)->first();
        $this->categoryOtherId = $categoryOther->id;
    }

    public function setOrganization($organizationId) {
        $this->organization = Organization::findOrFail($organizationId);
        $this->dispatchBrowserEvent('load-job-editors');
        $this->dispatchBrowserEvent('loadJobSubmissionLocationSearch');
    }

    protected function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'name' => 'required',
            'email' => ['required', 'email'],
            'phone' => 'nullable',
            'description' => 'required',
            'qualifications' => 'required',
            'salary_min' => 'required',
            'salary_max' => 'nullable',
            'type' => 'required',
            'remote' => 'required',
            'monthly' => 'required_without:recruiter',
            'recruiter' => 'required_without:monthly',
            'category' => 'required|notIn:placeholder',
            'category_other' => [
                Rule::requiredIf($this->category == $this->categoryOtherId),
            ],
            'organization' => 'required',
            'locations' => 'required|array',
        ];
    }

    protected $messages = [
        'category' => 'Please choose a category',
        'category_other' => 'Please enter a category',
        'email.required_without' => 'Please include your email or phone',
        'phone.required_without' => 'Please include your email or phone',
        'salary_min.required' => 'Starting salary is required',
        'locations.required' => 'Please enter at least one location'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updateJobLocation($locationName, $latitude, $longitude) {
        $this->locations[] = [
            'name' => $locationName,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
    }

    public function updateDescription($content) {
        $this->description = $content;
    }

    public function updateQualifications($content) {
        $this->qualifications = $content;
    }

    public function clearLocations() {
        $this->reset('locations');
    }

    public function submit()
    {
        $this->validate();

        /*
         * FUTURE TODO: Make hiring managers register??
         */

        $attributes = [
            'name' => $this->name,
            'description' => $this->description,
            'qualifications' => $this->qualifications,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'status' => Job::STATUS_PENDING,
            'posted_date' => Carbon::now(),
            'owner_type' => Organization::class,
            'owner_id' => $this->organization->id,
            'remote' => $this->remote,
            'type' => $this->type,
            'monthly' => $this->monthly,
            'recruiter' => $this->recruiter,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'category_id' => $this->category,
        ];

        try {
            $job = Job::create($attributes);

            $this->createOrUpdateUserAndPerson();
            $this->user->jobs()->attach($job);

            $locations = $this->createOrUpdateLocations();
            $job->locations()->syncWithoutDetaching($locations);

            $formFields = [
                'phone' => $this->phone,
                'organization' => $this->organization->name,
                'job_url' => route('admin.jobs.show', $job->slug)
            ];

            FormEntry::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'form' => FormEntry::FORM_JOB_POSTING,
                'fields' => json_encode($formFields)
            ]);

            $message = ":briefcase: *New Job Posted*\nHiring Manager: {$this->first_name} {$this->last_name}\n{$this->email} {$this->phone}\n" .
                "{$job->name} at {$job->owner->name}\n" .
                "<" . route('admin.jobs.show', $job->slug) . "|View Job>";

            SlackAlert::to('jobs')->message($message);

            $this->reset('error');
            $this->success = "Awesome! We've received your job posting and will get back to you as soon as possible. Thanks!";

            //redirect to subscription payment page
            if ($this->monthly) {
                $stripeSubscriptionUrl = config('services.stripe.job_posting_subscription_url') . '?client_reference_id=' . $job->id;

                $this->redirect($stripeSubscriptionUrl);
            }
        } catch(\Throwable $exception) {
            Log::critical('Error during job submission ' . $exception->getMessage(), $attributes);
            $this->error = "We're having trouble with your submission. Please double check the fields below and if there's still an issue, please contact sydney@spannr.com";
        }
    }

    public function render()
    {
        $categoryOptions = JobCategory::where('name', '!=', JobCategory::CATEGORY_OTHER)->orderBy('name')->get();

        return view('livewire.public.jobs.submit-job-posting', [
            'user' => $this->user,
            'categoryOptions' => $categoryOptions,
            'typeOptions' => Job::TYPES,
            'paymentOptions' => Job::PAYMENT_OPTIONS,
        ]);
    }

    private function createOrUpdateLocations(): array
    {
        $locationArray = [];

        foreach($this->locations as $place) {
            $location = Location::where('name', $place['name'])->first();

            if ($location) {
                $locationArray[] = $location->id;
            } else {
                $newLocation = Location::create([
                    'name' => $place['locationName'],
                    'latitude' => $place['locationLatitude'],
                    'longitude' => $place['locationLongitude']
                ]);
                $locationArray[] = $newLocation->id;
            }
        }

        return $locationArray;
    }

    private function createOrUpdateUserAndPerson() {
        //get or create user
        if ($this->user === null) {
            $this->user = User::where('email', $this->email)->first();

            if ($this->user === null) {
                $this->user = User::create([
                    'name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => Hash::make(Str::random()),
                ]);
            }

            Auth::login($this->user);
        }

        if ($this->user->hasRole(Role::ROLE_HIRING_MANAGER) === false) {
            $this->user->assignRole(Role::ROLE_HIRING_MANAGER);
        }

        //get or create person
        $person = $this->user->person;

        if (!$person) {
            $person = Person::where('email', $this->email)->first();

            if (!$person) {
                $person = Person::create([
                    'user_id' => $this->user->id,
                    'name' => $this->first_name . ' ' . $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ]);
            } else {
                $person->user_id = $this->user->id;
                $person->save();
            }
        }

        $tag = Tag::where('name', Tag::TAG_HIRING_MANAGER)->first();
        $person->tags()->syncWithoutDetaching([$tag->id]);
    }
}
