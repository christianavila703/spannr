<?php

namespace App\Http\Livewire\Admin\FormEntries;

use App\Models\FormEntry;
use Livewire\Component;
use Livewire\WithPagination;

class InvestorOptInIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
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
        return view('livewire.admin.form-entries.investor-opt-in-index', [
            'accreditedOptions' => FormEntry::INVESTOR_ACCREDITED_FIELDS,
            'formEntries' => FormEntry::investorOptin()
                ->orderByDesc('created_at')
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->investorOptin()
                        ->where(function($query) use ($search) {
                            $query->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('mailchimp_status', 'like', '%' . $search . '%')
                                ->orWhere('fields', 'like', '%' . $search . '%');
                        });
                })
                ->paginate($this->perPage)
        ]);
    }
}
