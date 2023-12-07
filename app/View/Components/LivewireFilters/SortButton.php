<?php

namespace App\View\Components\LivewireFilters;

use Illuminate\View\Component;

class SortButton extends Component
{
    public string $label;
    public string $field;
    public array $sorts;

    public function __construct($label, $field, $sorts)
    {
        $this->label = $label;
        $this->field = $field;
        $this->sorts = $sorts;
    }

    public function render()
    {
        return view('components.livewire-filters.sort-button');
    }
}
