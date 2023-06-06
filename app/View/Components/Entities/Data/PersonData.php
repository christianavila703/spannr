<?php

namespace App\View\Components\Entities\Data;

use App\Models\Person;
use Illuminate\View\Component;

class PersonData extends Component
{
    public Person $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

   public function render(): \Illuminate\Contracts\View\View|\Closure|string
    {
        return view('components.entities.data.person-data');
    }
}
