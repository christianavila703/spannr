<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Listing;
use App\Models\Tag;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class Languages extends Component
{
    public Listing $listing;
    public array $options = [];
    public array $current = [];
    public ?int $tagId = null;

    public ?string $search = null;

    public bool $createNew = false;
    public ?string $newItem = null;

    public function mount() {
        $parentTag = Tag::where('name', Tag::TAG_LANGUAGES)->firstOrFail();
        $this->tagId = $parentTag->id;
        $this->getAllOptions();
        $this->getCurrentOptions();
    }

    private function getCurrentOptions() {
        $this->listing->refresh();
        $this->current = $this->listing->languages->pluck('name', 'id')->toArray();
    }

    public function updatedSearch() {
        $this->options = Tag::where('parent_id', $this->tagId)->where('name', 'like', '%' . $this->search . '%')->pluck('name', 'id')->toArray();
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
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Please enter a language', 'background' => 'bg-danger']);
            return;
        }

        if (in_array($this->newItem, $this->options)) {
            $languageId = array_search($this->newItem, $this->options);
            $this->listing->languages()->syncWithoutDetaching([$languageId]);
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added language: ' . $this->newItem]);

            $this->getAllOptions();
            $this->getCurrentOptions();
            $this->reset('newItem');
            $this->reset('createNew');
            $this->reset('search');
            return;
        }

        try {
            $language = Tag::create([
                'name' => $this->newItem,
                'parent_id' => $this->tagId,
                'depth' => 1
            ]);

            SlackAlert::to('test')->message('<@sydney> *New Language Tag*' . "\n" .
                'Listing Type: ' . $this->listing->type . "\n" .
                'Language Tag: ' . $this->newItem . "\n" .
                $this->listing->show_url);

            $this->listing->languages()->syncWithoutDetaching([$language->id]);
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added language: ' . $this->newItem]);

        } catch (Throwable $exception) {
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'There was a problem adding this language. If the problem continues, please email help@spannr.com', 'background' => 'bg-danger']);

            SlackAlert::to('test')->message('<@sydney> *Problem Adding Language*' . "\n" .
                'Listing Type: ' . $this->listing->type . "\n" .
                'Language Tag: ' . $this->newItem . "\n" .
                '```' . $exception->getMessage() . '```' . "\n" .
                $this->listing->show_url);
        }

        $this->getAllOptions();
        $this->getCurrentOptions();
        $this->reset('newItem');
        $this->reset('createNew');
        $this->reset('search');
    }

    public function addOption($languageId, $language) {
        $this->listing->languages()->attach($languageId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added language: ' . $language, 'background' => 'bg-success']);
    }

    public function removeOption($languageId, $language) {
        $this->listing->languages()->detach($languageId);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed language: ' . $language, 'background' => 'bg-danger']);
    }

    private function getAllOptions() {
        $this->options = Tag::where('parent_id', $this->tagId)->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.languages');
    }
}
