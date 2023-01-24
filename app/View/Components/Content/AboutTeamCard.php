<?php

namespace App\View\Components\Content;

use App\Models\User;
use Illuminate\View\Component;

class AboutTeamCard extends Component
{
    public User $person;

    public function __construct(User $person)
    {
        $this->person = $person;
    }

    public function render(): \Illuminate\Contracts\View\View|string|\Closure
    {
        return view('components.content.about-team-card');
    }
}
