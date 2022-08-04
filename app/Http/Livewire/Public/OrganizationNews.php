<?php

namespace App\Http\Livewire\Public;

use App\Models\Article;
use App\Models\MediaItem;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationNews extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 5;
    public $organizationId;

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

    public function gotoPage($page)
    {
        $this->setPage($page);
        $this->emit('gotoTop');
    }

    public function nextPage()
    {
        $this->setPage($this->page + 1);
        $this->emit('gotoTop');
    }

    public function previousPage()
    {
        $this->setPage(max($this->page - 1, 1));
        $this->emit('gotoTop');
    }

    public function render()
    {
        return view('livewire.public.organization-news', [
            'mediaItems' => MediaItem::orderByDesc('date')
                ->whereHas('organizations', function (Builder $query) {
                    $query->where('id', $this->organizationId);
                })
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('summary', 'like', '%' . $search . '%');
                })
                ->paginate($this->perPage)
        ]);
    }
}
