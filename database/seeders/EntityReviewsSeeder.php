<?php

namespace Database\Seeders;

use App\Models\EntityReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntityReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entityTypes = config('entity-reviews.entity_types');
        $requiredReviewers = config('entity-reviews.required_reviewers');
        $reviewers = User::whereIn('email', ['cody@spannr.com', 'sydney@spannr.com'])->get();

        if ($reviewers->count() < $requiredReviewers) {
            $this->command->error('Not enough users to add the minimum number of reviews.');
            return;
        }

        foreach ($entityTypes as $entityClass) {
            $entitesForReview = $entityClass::whereDoesntHave('reviews')
                ->where(function($query) {
                    return $query
                        ->where('created_at', '<', '2023-01-01')
                        ->orWhereNull('created_at');
                })
                ->get();

            foreach ($entitesForReview as $entity) {
                foreach ($reviewers as $reviewer) {
                    EntityReview::create([
                        'entity_type' => $entity::class,
                        'entity_id' => $entity->id,
                        'reviewer_id' => $reviewer->id,
                    ]);
                }
            }
        }

    }
}
