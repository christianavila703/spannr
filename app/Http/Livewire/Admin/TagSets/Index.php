<?php

namespace App\Http\Livewire\Admin\TagSets;

use App\Models\TagSet;
use Livewire\Component;

class Index extends Component
{
    public $name;
    public $description;

    public function rules() {
        return [
            'name' => 'required|unique:tag_sets',
            'description' => 'required'
        ];
    }

    public function submit() {
        $this->validate();

        TagSet::create([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->reset('name');
        $this->reset('description');
    }

    public function render()
    {
        return view('livewire.admin.tag-sets.index', [
            'records' => TagSet::get()
        ]);
    }
}
