<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Tag;
use App\Models\TagSet;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;

class MedicalTreatments extends Component
{
    public Listing $listing;
    public array $options = [];
    public array $current = [];

    public ?string $search = null;

    public bool $createNew = false;
    public ?string $newItem = null;

    public function mount() {
        $this->getAllOptions();
        $this->getCurrentOptions();
    }

    public function getCurrentOptions() {
        $this->listing->refresh();
        $this->current = $this->listing->treatments->pluck('name', 'id')->toArray();
    }

    public function updatedSearch() {
        $this->options = Tag::whereHas('tagSets', function (Builder $query) {
            $query->where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS);
        })->where('name', 'like', '%' . $this->search . '%')->pluck('name', 'id')->toArray();
    }

    public function clearSearch() {
        $this->reset('search');
        $this->getAllOptions();
    }

    public function toggleCreateForm() {
        $this->createNew = !$this->createNew;
    }

    public function createItem() {
        if ($this->newItem == '') {
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Please enter a treatment or service', 'background' => 'bg-danger']);
            return;
        }

        $existingTag = Tag::where('name', $this->newItem)->first();
        $tagSet = TagSet::where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS)->first();

        if ($existingTag) {
            $existingTag->tagSets()->syncWithoutDetaching([$tagSet->id]);
            $this->listing->treatments()->syncWithoutDetaching([$existingTag->id]);

            SlackAlert::to('test')->message('<@sydney> *Existing Tag Assigned to Treatments*' . "\n" .
                'Listing Type: ' . $this->listing->type . "\n" .
                'Treatment Tag: ' . $this->newItem . "\n" .
                $this->listing->show_url);

        } else {
            $tag = Tag::create([
                'name' => $this->newItem
            ]);

            SlackAlert::to('test')->message('<@sydney> *New Tag Assigned to Treatments*' . "\n" .
                'Listing Type: ' . $this->listing->type . "\n" .
                'Treatment Tag: ' . $this->newItem . "\n" .
                $this->listing->show_url);

            $tag->tagSets()->syncWithoutDetaching([$tagSet->id]);
            $this->listing->treatments()->syncWithoutDetaching([$tag->id]);
        }

        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added treatment: ' . $this->newItem]);

        $this->getAllOptions();
        $this->getCurrentOptions();
        $this->reset('newItem');
        $this->reset('createNew');
        $this->reset('search');
    }

    public function addOption($tagId, $tagName) {
        $this->listing->treatments()->attach($tagId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added treatment: ' . $tagName, 'background' => 'bg-success']);
    }

    public function removeOption($tagId, $tagName) {
        $this->listing->treatments()->detach($tagId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed treatment: ' . $tagName, 'background' => 'bg-danger']);
    }

    private function getAllOptions() {
        $this->options = Tag::whereHas('tagSets', function (Builder $query) {
            $query->where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS);
        })->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.medical-treatments');
    }
}
