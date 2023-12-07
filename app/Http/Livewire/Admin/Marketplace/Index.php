<?php

namespace App\Http\Livewire\Admin\Marketplace;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Listing;
use App\Models\Tag;
use App\Models\TagSet;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected string $paginationTheme = 'bootstrap';
    protected $queryString = ['search'];

    public ?string $search = null;

    public array $filters = [
        'treatment' => [],
        'type' => [],
        'status' => []
    ];

    public bool $virtual = false;

    public function clearSearch() {
        $this->reset('search');
        $this->resetPage();
    }

    public function clearFilters() {
        $this->reset('search');
        $this->reset('filters');
        $this->reset('sorts');
    }

    public function getRowsQueryProperty()
    {
        $query = Listing::with([
                'addresses',
                'ageGroups',
                'languages',
                'primaryAddress',
                'treatments',
                'marketplaceLogs',
                'formEntries',
                'websiteVisits',
                'listingClicks'
            ])
            ->withCount([
                'addresses',
                'ageGroups',
                'languages',
                'primaryAddress',
                'treatments',
                'marketplaceLogs',
                'formEntries',
                'websiteVisits',
                'listingClicks'
            ])
            ->when($this->search, function ($query, $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orwhereHas('addresses', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orwhereHas('treatments', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($this->filters['type'], function($query, $valueArray) {
                return $query->whereIn('type', $valueArray);
            })
            ->when($this->filters['status'], function($query, $valueArray) {
                return $query->whereIn('status', $valueArray);
            })
            ->when($this->virtual, function($query) {
                return $query->where('virtual', 1);
            })
            ->when($this->filters['treatment'], function($query, $valueArray) {
                return $query->whereHas('treatments', function($query) use ($valueArray) {
                    $query->whereIn('name', $valueArray);
                });
            });

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.admin.marketplace.index', [
            'records' => $this->rows,
            'statusOptions' => Listing::STATUSES,
            'treatmentOptions' => Tag::whereHas('tagSets', function (Builder $query) {
                    $query->where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS);
                })->whereHas('listings')->get()->toArray(),
            'typeOptions' => Listing::TYPES
        ]);
    }
}
