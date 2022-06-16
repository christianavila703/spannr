<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    protected $table = 'activity_log';

    const LOG_USERS = 'users';
    const LOG_STOCK_SNAPSHOTS = 'stock snapshots';
    const LOG_MARKET_SNAPSHOTS = 'market snapshots';
    const LOG_STOCKS = 'stocks';
    const LOG_PEOPLE = 'people';
    const LOG_ORGANIZATIONS = 'organizations';
    const LOG_INVESTORS = 'investors';
    const LOG_FUNDRAISING_ROUNDS = 'fundraising rounds';

    const LOG_OPTIONS = [
        self::LOG_ORGANIZATIONS,
        self::LOG_INVESTORS,
        self::LOG_FUNDRAISING_ROUNDS,
        self::LOG_PEOPLE,
        self::LOG_STOCKS,
        self::LOG_MARKET_SNAPSHOTS,
        self::LOG_STOCK_SNAPSHOTS,
        self::LOG_USERS,
    ];

    public function getSubjectLinkAttribute(): string
    {
        $routeName = match($this->subject_type) {
            Organization::class => 'organizations.show',
            Person::class => 'people.show',
            Investor::class => 'investors.show',
            FundraisingRound::class => 'fundraising-rounds.show',
            default => ''
        };

        $routeParam = match($this->subject_type) {
            Organization::class, Person::class, Investor::class => $this->subject->slug,
            FundraisingRound::class => $this->subject->id ?? '',
            default => ''
        };

        try {
            $route = route($routeName, $routeParam);
            $subjectLink = '<a href="' . $route . '">' . $this->subject->name . '</a>';
        } catch (\Throwable $throwable) {
            Log::critical('Invalid route for activity # ' . $this->id);
            $subjectLink = '';
        }

        return $subjectLink;
    }

    public function getCauserLinkAttribute(): string
    {
        if ($this->causer_type === User::class) {
            return '<a href="">' . $this->causer->name . '</a>';;
        } else {
            return '';
        }
    }
}
