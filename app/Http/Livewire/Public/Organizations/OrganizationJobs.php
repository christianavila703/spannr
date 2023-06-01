<?php

namespace App\Http\Livewire\Public\Organizations;

use App\Models\Job;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class OrganizationJobs extends Component
{
    protected string $paginationTheme = 'bootstrap';

    public int $organizationId;

    public function render()
    {
        return view('livewire.public.organizations.organization-jobs', [
            'records' => Job::open()->whereHasMorph(
                            'owner',
                            [Organization::class],
                            function(Builder $query) {
                                $query->where('id', $this->organizationId);
                            }
                        )->take(3)
                        ->get()
        ]);
    }
}
