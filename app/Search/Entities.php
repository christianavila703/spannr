<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;
use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;

class Entities extends Aggregator
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        Organization::class,
        Person::class,
        Investor::class,
        FundraisingRound::class,
        Tag::class,
        Job::class,
        Event::class
    ];

    /**
     * Map of model relations to load.
     *
     * @var string[]
     */
    protected $relations = [
        Organization::class => [
            'fundraisingRounds',
            'investors',
            'founders',
            'team'
        ],
        Person::class => ['organizations'],
        Investor::class => [
            'fundraisingRounds',
            'organizations'
        ],
        FundraisingRound::class => [
            'investors',
            'organization'
        ]
    ];
}
