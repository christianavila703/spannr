<?php

namespace App\Http\Livewire\Public\Organizations;

use App\Models\MediaItem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationNews extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $organizationId;

    public function render()
    {
        return view('livewire.public.organizations.organization-news', [
            'mediaItems' => MediaItem::orderByDesc('date')
                ->whereHas('organizations', function (Builder $query) {
                    $query->where('id', $this->organizationId);
                })
                ->paginate(5, ['*'], 'newsPage')
        ]);
    }
}
