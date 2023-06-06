<?php

namespace App\View\Components\Entities\Data;

use App\Models\Organization;
use Illuminate\View\Component;

class OrganizationData extends Component
{
    public Organization $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|\Closure|string|\Illuminate\Contracts\Foundation\Application
    {
        $hasStockChart = ($this->organization->stock('stock')->count() > 0);
        $latestSnapshot = $this->organization->stock->latestSnapshot ?? null;

        return view('components.entities.data.organization-data', [
            'hasStockChart' => $hasStockChart,
            'latestSnapshot' => $latestSnapshot
        ]);
    }
}
