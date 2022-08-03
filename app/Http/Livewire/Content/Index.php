<?php

namespace App\Http\Livewire\Content;

use App\Models\Content;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
        'selected-options' => []
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
        return view('livewire.content.index', [
            'statusOptions' => Content::pluck('status')->unique()->toArray(),
            'pageSlugs' => Content::PAGES,
            'content' => Content::orderByDesc('updated_at')
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%');
                })
                ->when($this->filters['selected-options'], function($query, $selectedOptions) {
                    return $query->whereIn('status', $selectedOptions);
                })
                ->paginate($this->perPage)
        ]);
    }
}
