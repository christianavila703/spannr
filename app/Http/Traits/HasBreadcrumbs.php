<?php

namespace App\Http\Traits;

use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;

trait HasBreadcrumbs {
    public function getBreadcrumbAttribute(): string
    {
        if ($this instanceof Tag) {
            return $this->getBreadcrumbsForTags();
        } elseif($this instanceof FundraisingRound) {
            return $this->getBreadcrumbsForFundraisingRounds();
        } elseif($this instanceof Job) {
            return $this->getBreadcrumbsForJobs();
        } else {
            return $this->getBreadcrumbsForTypicalEntities();
        }
    }

    private function getBreadcrumbsForTags(): string
    {
        $breadcrumb = '<a href="' . route('admin.tags.index') . '">' . 'Tags</a>' . '<i class="fa-light fa-angle-right mx-2"></i>';

        if ($this->parent) {
            if ($this->parent->parent) {
                $breadcrumb .= '<a href="' . route('admin.tags.show', $this->parent->parent->slug) . '">' . $this->parent->parent->name . '</a><i class="fa-light fa-angle-right mx-2"></i>';
            }
            $breadcrumb .= '<a href="' . route('admin.tags.show', $this->parent->slug) . '">' . $this->parent->name . '</a><i class="fa-light fa-angle-right mx-2"></i>';
        }

        $breadcrumb .= $this->name;

        return $breadcrumb;
    }

    private function getBreadcrumbsForFundraisingRounds() {
        $breadcrumb = '<a href="' . route('admin.fundraising-rounds.index') . '">' . 'Fundraising Rounds</a>' . '<i class="fa-light fa-angle-right mx-2"></i>';
        $breadcrumb .= '<a href="' . route('admin.organizations.show', $this->organization->slug) . '">' . $this->organization->name . '</a><i class="fa-light fa-angle-right mx-2"></i>';
        $breadcrumb .= $this->name;
        return $breadcrumb;
    }

    private function getBreadcrumbsForJobs() {
        $ownerRoute = match(get_class($this->owner)) {
            Organization::class => 'admin.organizations.show',
            Investor::class => 'admin.investors.show',
        };

        $breadcrumb = '<a href="' . route('admin.jobs.index') . '">' . 'Jobs</a>' . '<i class="fa-light fa-angle-right mx-2"></i>';
        $breadcrumb .= '<a href="' . route($ownerRoute, $this->owner->slug) . '">' . $this->owner->name . '</a><i class="fa-light fa-angle-right mx-2"></i>';
        $breadcrumb .= $this->name;
        return $breadcrumb;
    }

    private function getBreadcrumbsForTypicalEntities() {

        $rootRoute = match(get_class($this)) {
            Organization::class => 'admin.organizations.index',
            Person::class => 'admin.people.index',
            Investor::class => 'admin.investors.index',
            Event::class => 'admin.events.index',
            Job::class => 'admin.jobs.index',
            FundraisingRound::class => 'admin.fundraising-rounds.index'
        };

        $rootName = match(get_class($this)) {
            Organization::class => 'Organizations',
            Person::class => 'People',
            Investor::class => 'Investors',
            Event::class => 'Events',
            Job::class => 'Jobs',
            FundraisingRound::class => 'Fundraising Rounds'
        };

        $breadcrumb = '<a href="' . route($rootRoute) . '">' . $rootName . '</a>' . '<i class="fa-light fa-angle-right mx-2"></i>';

        $breadcrumb .= $this->name;

        return $breadcrumb;
    }
}
