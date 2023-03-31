<?php

return [
    'admin' => [
        \App\Models\Organization::class => [
            'show' => 'admin.organizations.show',
            'edit' => 'admin.organizations.edit',
        ],
        \App\Models\FundraisingRound::class => [
            'show' => 'admin.fundraising-rounds.show',
            'edit' => 'admin.fundraising-rounds.edit',
        ],
        \App\Models\Person::class => [
            'show' => 'admin.people.show',
            'edit' => 'admin.people.edit',
        ],
        \App\Models\Investor::class => [
            'show' => 'admin.investors.show',
            'edit' => 'admin.investors.edit',
        ],
        \App\Models\Event::class => [
            'show' => 'admin.events.show',
            'edit' => 'admin.events.edit',
        ],
        \App\Models\Job::class => [
            'show' => 'admin.jobs.show',
            'edit' => 'admin.jobs.edit',
        ],
        \App\Models\Content::class => [
            'show' => 'admin.content.show',
            'edit' => 'admin.content.edit',
        ],
    ]
];
