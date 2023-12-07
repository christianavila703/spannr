<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Tag;
use Livewire\Component;

class AgeGroups extends Component
{
    public Listing $listing;
    public array $options = [];
    public array $current = [];
    public ?int $tagId = null;

    public function mount() {
        $parentTag = Tag::where('name', Tag::TAG_AGE_GROUPS)->firstOrFail();
        $this->tagId = $parentTag->id;
        $this->options = Tag::where('parent_id', $this->tagId)->pluck('name', 'id')->toArray();
        $this->getCurrentOptions();
    }

    private function getCurrentOptions() {
        $this->listing->refresh();
        $this->current = $this->listing->ageGroups->pluck('name', 'id')->toArray();
    }

    public function addOption($tagId, $tagName) {
        $this->listing->ageGroups()->attach($tagId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added ' . $tagName, 'background' => 'bg-success']);
    }

    public function removeOption($tagId, $tagName) {
        $this->listing->ageGroups()->detach($tagId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed ' . $tagName, 'background' => 'bg-danger']);
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.age-groups');
    }
}
