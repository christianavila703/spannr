<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Person;

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
        FundraisingRound::class
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
