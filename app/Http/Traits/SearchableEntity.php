<?php

namespace App\Http\Traits;

use Laravel\Scout\Searchable;

trait SearchableEntity {

    use Searchable;

    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        $routeName = $this->searchableRouteName;
        $routeField = $this->searchableRouteField;

        $url = '';

        try {
            $url = route($routeName, $array[$routeField]);
        } catch(\Throwable $throwable) {

        }

        $array['spannr_url'] = $url;
        $array['icon'] = $this->searchableIcon;

        return $array;
    }
}
