<?php

namespace App\View\Components\LivewireFilters;

use Illuminate\View\Component;

class Search extends Component
{
    public string $label;
    public string $placeholder;
    public string $search;
    public string $tooltip;

    public function __construct(string $label, string $placeholder, string $search, string $tooltip = 'Search by name, keyword, location, focus...')
    {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->search = $search;
        $this->tooltip = $tooltip;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|\Closure|string|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.livewire-filters.search');
    }
}
