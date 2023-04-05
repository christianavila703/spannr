<?php

namespace App\Http\Livewire\Public\Jobs;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Location;
use App\Models\Person;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\SlackAlerts\Facades\SlackAlert;

class Apply extends Component
{
    use WithFileUploads;

    public $job;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $locationName;
    public $locationLatitude;
    public $locationLongitude;
    public $linkedin;
    public $message;
    public $recruiter;
    public $resume;

    public $error;
    public $success;

    protected $listeners = ['updateJobApplicantLocation'];

    protected function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email'],
            'phone' => 'required',
            'linkedin' => 'nullable',
            'resume' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'recruiter' => 'required',
            'locationName' => 'required',
            'locationLatitude' => 'required',
            'locationLongitude' => 'required',
        ];
    }

    public function messages() {
        return [
            'first_name.required' => 'Please enter your first name',
            'last_name.required' => 'Please enter your last name',
            'email.required' => 'Please enter your email',
            'phone.required' => 'Please enter your phone',
            'resume.required' => 'Please upload your resume',
            'recruiter.required' => 'Please choose an option',
            'locationName.required' => 'Please select a location'
        ];
    }

    public function updateJobApplicantLocation($locationName, $latitude, $longitude) {
        $this->locationName = $locationName;
        $this->locationLatitude = $latitude;
        $this->locationLongitude = $longitude;
    }

    public function submit() {
        $this->validate();

        try {
            $person = $this->findOrCreatePerson();

            $location = $this->findOrCreateLocation();
            $person->locations()->syncWithoutDetaching([$location->id]);

            $tag = Tag::where('name', Tag::TAG_JOB_APPLICANT)->first();
            $person->tags()->syncWithoutDetaching([$tag->id]);

            $jobApplication = JobApplication::create([
                'person_id' => $person->id,
                'job_id' => $this->job !== null ? $this->job->id : null,
                'location_id' => $location->id,
                'message' => $this->message,
                'recruiter' => $this->recruiter,
            ]);

            $jobApplication->addMedia($this->resume->getRealPath())
                ->setName($person->name . ' Resume ' . ($this->job ? $this->job->owner->name : ''))
                ->usingFileName($person->slug . '-resume.' . $this->resume->extension())
                ->toMediaCollection('resume');

            if ($this->recruiter === 1) {
                $this->success = "We've received your job application. Our recruiting team will be in touch soon. Thanks!";
            } else {
                $this->success = "We've received your job application. We'll forward it to the hiring company. Thanks!";
            }

            $jobDescriptionMessage = "General job";

            if ($jobApplication->job) {
                $jobDescriptionMessage = $jobApplication->job->name  . " for " . $jobApplication->job->owner->name;
            }

            SlackAlert::to('jobs')->message("*Job Application from {$jobApplication->person->name}*" . "\n" .
                $jobDescriptionMessage . "\n" . "<{$jobApplication->admin_link_url}|View Application>");

            $this->reset('error');

        } catch(\Throwable $exception) {
            SlackAlert::message('<@sydney> Error during job application for ' . $this->email . ' ' . $exception->getMessage());
            $this->error = "We're having trouble with your submission. Please double check the fields below and if there's still an issue, please contact sydney@spannr.com";
        }

    }

    public function render()
    {
        return view('livewire.public.jobs.apply');
    }

    private function findOrCreatePerson() {
        return Person::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->first_name . ' ' . $this->last_name,
                'email' => $this->email,
                'linkedin' => $this->linkedin,
                'phone' => $this->phone
            ]
        );
    }

    private function findOrCreateLocation() {
        return Location::firstOrCreate(
            ['name' => $this->locationName],
            [
                'name' => $this->locationName,
                'latitude' => $this->locationLatitude,
                'longitude' => $this->locationLongitude
            ]
        );
    }
}
