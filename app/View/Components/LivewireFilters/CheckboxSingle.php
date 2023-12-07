<?php

namespace App\View\Components\LivewireFilters;

use Illuminate\View\Component;

class CheckboxSingle extends Component
{
    public string $wireModel;
    public string $id;
    public string $label;
    public string $class;

    public function __construct(string $wireModel, string $id, string $label, string $class = 'lead')
    {
        $this->wireModel = $wireModel;
        $this->id = $id;
        $this->label = $label;
        $this->class = $class;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|string|\Closure|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.livewire-filters.checkbox-single');
    }
}
