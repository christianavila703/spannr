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

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.entities.data.person-data');
    }
}
