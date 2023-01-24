<?php

namespace App\Http\Livewire\Public;

use App\Models\Glossary;
use Livewire\Component;

class RandomGlossaryItemWidget extends Component
{
    public Glossary $glossary;
    public array $excludedIds = [];

    public function randomize() {
        $this->glossary = Glossary::inRandomOrder()
            ->when($this->excludedIds, function($query, $excludedIds) {
                return $query
                    ->whereNotIn('id', $excludedIds);
            })
            ->first();
    }

    public function mount() {
        $this->randomize();
    }

    public function render()
    {
        return view('livewire.public.random-glossary-item-widget');
    }
}
