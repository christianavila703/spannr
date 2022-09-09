<?php

namespace App\Http\Livewire\Admin\FormEntries;

use App\Models\FormEntry;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscribers extends Component
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
        return view('livewire.admin.form-entries.newsletter-subscribers', [
            'formEntries' => FormEntry::newsletterOptin()
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
                ->paginate($this->perPage, ['*'], 'newsletterPage')
        ]);
    }
}
