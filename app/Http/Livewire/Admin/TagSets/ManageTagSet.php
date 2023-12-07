<?php

namespace App\Http\Livewire\Admin\TagSets;

use App\Models\Tag;
use App\Models\TagSet;
use Livewire\Component;

class ManageTagSet extends Component
{
    public TagSet $tagSet;
    public array $options;
    public array $current = [];

    public function mount() {
        $this->options = Tag::firstLevel()
            ->with(['children'])
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->getCurrentOptions();
    }

    private function getCurrentOptions() {
        $this->tagSet->refresh();
        $this->current = $this->tagSet->tags->pluck('name', 'id')->toArray();
    }

    public function addOption($tagId) {
        $this->tagSet->tags()->attach($tagId);
        $this->getCurrentOptions();
    }

    public function removeOption($tagId) {
        $this->tagSet->tags()->detach($tagId);
        $this->getCurrentOptions();
    }

    public function render()
    {
        return view('livewire.admin.tag-sets.manage-tag-set');
    }
}
