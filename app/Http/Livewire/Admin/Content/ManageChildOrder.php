<?php

namespace App\Http\Livewire\Admin\Content;

use App\Models\Content;
use Livewire\Component;

class ManageChildOrder extends Component
{
    public Content $content;
    public $children;
    public string $success = '';

    protected $rules = [
        'children.*.order' => 'required|int'
    ];

    public function mount() {
        $this->children = $this->content->children;
    }

    public function save()
    {
        $this->validate();

        foreach ($this->children as $childContent) {
            $childContent->save();
        }

        $this->success = 'Updated!';
    }

    public function render()
    {
        return view('livewire.admin.content.manage-child-order');
    }
}
