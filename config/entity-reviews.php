<?php

return [

    /*
    | Entities which enabled for review
    */
    'entity_types'  => [
        'organization' => \App\Models\Organization::class,
        'fundraising_round' => \App\Models\FundraisingRound::class,
    ],

    /*
    | Total required reviewers
    */
    'required_reviewers' => 2,

    /*
    | Require audit if Entity Review older than * days
    */
    'audit_after' => 180,

];
