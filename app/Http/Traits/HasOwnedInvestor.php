<?php

namespace App\Http\Traits;

use App\Models\Investor;

trait HasOwnedInvestor {
    public function ownedInvestor(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Investor::class, 'ownable');
    }

    public function getAdminLinkAttribute(): string
    {
        return route($this->searchableRouteName, $this->attributes[$this->searchableRouteField]);
    }

    public function getIconAttribute(): string
    {
        return $this->searchableIcon;
    }

    public function getInvestorIconAttribute(): string
    {
        return Investor::ICON_CSS_CLASS;
    }
}
