<?php

namespace App\Http\Livewire\Admin\Imports\Listings;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Jobs\Imports\ProcessImportedListing;
use App\Models\ImportResult;
use App\Models\Listing;
use Livewire\Component;

class ImportedListings extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public ?string $search = null;

    public $selectedRecord;

    public $filters = [
        'status' => [],
        'type' => [],
        'errors-exception' => false,
        'errors-tags' => false,
        'errors-address' => false,
        'errors-email' => false
    ];

    public function processImports() {
        $count = $this->selectedRowsQuery->count();
        $records =$this->selectedRowsQuery->get();

        foreach($records as $record) {
            if ($record->type === ImportResult::TYPE_LISTING_IMPORT) {
                ProcessImportedListing::dispatch($record);
            }
        }

        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Processing ' . $count . ' records', 'background' => 'bg-success']);
        $this->reset('selected');
        $this->reset('selectAll');
        $this->reset('selectPage');
    }

     public function makePublic() {
        $count = $this->selectedRowsQuery->count();
        $records =$this->selectedRowsQuery->get();

        foreach($records as $record) {
            if ($record->entity) {
                $record->entity->update(['status' => Listing::STATUS_PUBLIC]);
            }
        }

        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Making ' . $count . ' listings public', 'background' => 'bg-success']);
        $this->reset('selected');
        $this->reset('selectAll');
        $this->reset('selectPage');
    }

    public function deleteImports() {
        $count = $this->selectedRowsQuery->count();
        $records =$this->selectedRowsQuery->delete();
        $this->reset('selected');
        $this->reset('selectAll');
        $this->reset('selectPage');
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Deleted ' . $count . ' records', 'background' => 'bg-danger']);
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function updatingFilters() {
        $this->resetPage();
    }

    public function clearSearch() {
        $this->reset('search');
        $this->resetPage();
    }

    public function clearFilters() {
        $this->reset('search');
        $this->reset('filters');
        $this->reset('sorts');
        $this->reset('selected');
        $this->reset('selectAll');
        $this->reset('selectPage');
        $this->resetPage();
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

    public function updatedPerPage() {
        $this->resetPage();
        $this->emit('gotoTop');
    }

    public function getRowsQueryProperty()
    {
        $query = ImportResult::orderByDesc('created_at')
                ->listings()
                ->with(['entity'])
                ->when($this->search, function($query, $search) {
                    return $query
                        ->where('type', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('entity', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['status'], function($query, $valueArray) {
                    return $query->whereIn('status', $valueArray);
                })
            ->when($this->filters['type'], function($query, $valueArray) {
                    return $query->whereIn('type', $valueArray);
                })
                ->when($this->filters['errors-exception'], function($query) {
                    return $query->whereNotNull('exception_errors');
                })
                ->when($this->filters['errors-tags'], function($query) {
                    return $query->whereNotNull('tag_errors');
                })
                ->when($this->filters['errors-address'], function($query) {
                    return $query->whereNotNull('address_errors');
                })
                ->when($this->filters['errors-email'], function($query) {
                    return $query->whereNotNull('email_errors');
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
        return view('livewire.admin.imports.listings.imported-listings', [
            'records' => $this->rows,
            'statusOptions' => ImportResult::orderBy('status')->pluck('status')->unique()->toArray(),
            'typeOptions' => [ImportResult::TYPE_LISTING_BATCH, ImportResult::TYPE_LISTING_IMPORT]
        ]);
    }
}
