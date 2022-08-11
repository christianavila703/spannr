<?php

namespace App\Http\Livewire\Admin\Tags;

use App\Models\Organization;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;

    public $filters = [
        'search' => '',
    ];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.admin.tags.index', [
            'tags' => Tag::where('name', 'like', '%' . $this->filters['search'] . '%')
                ->orWhere('description', 'like', '%' . $this->filters['search'] . '%')
                ->paginate($this->perPage)
        ]);
    }
}
