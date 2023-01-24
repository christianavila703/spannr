<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserProfile extends Component
{
    use WithFileUploads;

    public User $user;

    public $profilePhoto;
    public $teamPhoto;
    public bool $saved = false;

    protected $rules = [
        'user.name' => 'required|string|min:1|max:100',
        'user.last_name' => 'required|string|min:1|max:100',
        'user.title' => 'required|string|min:1|max:80',
        'user.byline' => 'nullable|string|min:1|max:80',
        'user.bio' => 'nullable|string|max:600',
        'profilePhoto' => 'nullable|image|max:1024',
        'teamPhoto' => 'nullable|image|max:1024',
    ];

    public function save()
    {
        $this->validate();
        $this->user->save();

        if ($this->profilePhoto) {
            $this->user
                ->addMedia($this->profilePhoto->getRealPath())
                ->setName($this->user->full_name)
                ->usingFileName(Str::slug($this->user->full_name) . '.' . $this->profilePhoto->extension())
                ->toMediaCollection('profilePhoto');
        }

        if ($this->teamPhoto) {
            $this->user
                ->addMedia($this->teamPhoto->getRealPath())
                ->setName($this->user->full_name)
                ->usingFileName(Str::slug($this->user->full_name) . '.' . $this->teamPhoto->extension())
                ->toMediaCollection('teamPhoto');
        }

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.users.user-profile', [
            'currentProfilePhoto' => $this->user->getFirstMediaUrl('profilePhoto'),
            'currentTeamPhoto' => $this->user->getFirstMediaUrl('teamPhoto')
        ]);
    }
}
