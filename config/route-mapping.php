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
        ]
    ]
];
