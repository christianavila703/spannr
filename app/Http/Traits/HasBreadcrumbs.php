<?php

namespace App\Http\Traits;

use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;

trait HasBreadcrumbs {

    public function generateBreadcrumbs($rootRouteName = 'admin'): array
    {
        if (!in_array($rootRouteName, ['admin', 'panel'])) {
            return [];
        }

        if ($this instanceof Tag) {
            $breadcrumbsArray = $this->getBreadcrumbsForTags();
        } elseif($this instanceof FundraisingRound) {
            $breadcrumbsArray = $this->getBreadcrumbsForFundraisingRounds();
        } elseif($this instanceof Job) {
            $breadcrumbsArray = $this->getBreadcrumbsForJobs();
        } else {
            $breadcrumbsArray = $this->getBreadcrumbsForTypicalEntities();
        }

        $breadcrumbs = [];
        //dashboard part only for panel type
        if ($rootRouteName == 'panel') {
            $breadcrumbs[] = [
                'routeName' => 'panel.index',
                'routeParameter' => null,
                'label' => 'Spannr+',
            ];
        }

        foreach ($breadcrumbsArray as $item) {
            $breadcrumbs[] = [
                'routeName' => $rootRouteName . '.' . $item['routeName'],
                'routeParameter' => $item['routeParameter'],
                'label' => $item['label'],
            ];
        }

        //current entity without route
        $breadcrumbs[] = ['label' => $this->name];

        return $breadcrumbs;
    }

    private function getBreadcrumbsForTags(): array
    {
        $breadcrumbs[] = [
            'routeName' => 'tags.index',
            'routeParameter' => null,
            'label' => 'Tags',
        ];

        if ($this->parent) {
            if ($this->parent->parent) {
                $breadcrumbs[] = [
                    'routeName' => 'tags.show',
                    'routeParameter' => $this->parent->parent->slug,
                    'label' => $this->parent->parent->name,
                ];
            }

            $breadcrumbs[] = [
                'routeName' => 'tags.show',
                'routeParameter' => $this->parent->slug,
                'label' => $this->parent->name,
            ];
        }

        return $breadcrumbs;
    }

    private function getBreadcrumbsForFundraisingRounds(): array {
        $breadcrumbs[] = [
            'routeName' => 'fundraising-rounds.index',
            'routeParameter' => null,
            'label' => 'Fundraising Rounds',
        ];

        $breadcrumbs[] = [
            'routeName' => 'organizations.show',
            'routeParameter' => $this->organization->slug,
            'label' => $this->organization->name,
        ];

        return $breadcrumbs;
    }

    private function getBreadcrumbsForJobs(): array {
        $ownerRoute = match(get_class($this->owner)) {
            Organization::class => 'organizations.show',
            Investor::class => 'investors.show',
        };

        $breadcrumbs[] = [
            'routeName' => 'jobs.index',
            'routeParameter' => null,
            'label' => 'Jobs',
        ];

        $breadcrumbs[] = [
            'routeName' => $ownerRoute,
            'routeParameter' => $this->owner->slug,
            'label' => $this->owner->name,
        ];

        return $breadcrumbs;
    }

    private function getBreadcrumbsForTypicalEntities(): array {

        $route = match(get_class($this)) {
            Organization::class => 'organizations.index',
            Person::class => 'people.index',
            Investor::class => 'investors.index',
            Event::class => 'events.index',
            Job::class => 'jobs.index',
            FundraisingRound::class => 'fundraising-rounds.index',
            Location::class => 'locations.index',
        };

        $label = match(get_class($this)) {
            Organization::class => 'Organizations',
            Person::class => 'People',
            Investor::class => 'Investors',
            Event::class => 'Events',
            Job::class => 'Jobs',
            FundraisingRound::class => 'Fundraising Rounds',
            Location::class => 'Locations',
        };

        $breadcrumbs[] = [
            'routeName' => $route,
            'routeParameter' => null,
            'label' => $label,
        ];

        return $breadcrumbs;
    }
}
