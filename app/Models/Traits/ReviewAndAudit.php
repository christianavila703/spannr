<?php

namespace App\Models\Traits;

use App\Models\EntityReview;
use App\Models\EntityReviewNote;
use Carbon\Carbon;

trait ReviewAndAudit
{
    public function reviews()
    {
        return $this->morphMany(EntityReview::class, 'entity');
    }

    public function reviewNote()
    {
        return $this->morphOne(EntityReviewNote::class, 'entity');
    }

    public function scopeUnreviewedByUserId($query, $userId)
    {
        $requiredReviewers = config('entity-reviews.required_reviewers');

        return $query->whereDoesntHave('reviews')
            ->orWhereHas('reviews', function ($query) use ($userId) {
                return $query->where('reviewer_id', '!=', $userId);
            })->withCount('reviews')->having('reviews_count', '<', $requiredReviewers);
    }

    public function scopeReviewedNotApprovedByUserId($query, $userId)
    {
        $requiredReviewers = config('entity-reviews.required_reviewers');

        return $query->whereHas('reviews', function ($query) use ($userId) {
                return $query->where('reviewer_id', '=', $userId);
            })->withCount('reviews')
            ->having('reviews_count', '<', $requiredReviewers);
    }


    public function scopeAuditRequired($query)
    {
        $requiredAuditAfter = config('entity-reviews.audit_after');

        return $query->whereHas('reviews', function ($query) use ($requiredAuditAfter) {
            return $query->where('updated_at', '<', Carbon::now()->subDays($requiredAuditAfter));
        });
    }

    public function getIsNeedReviewAttribute()
    {
        return $this->reviews->count() < config('entity-reviews.required_reviewers');
    }

    public function getIsNeedAuditAttribute()
    {
        if ($this->is_need_review) {
            return false;
        }

        $requiredAuditAfter = config('entity-reviews.audit_after');
        $now = Carbon::now();

        foreach ($this->reviews as $review) {
            if ($now->diffInDays($review->updated_at) > $requiredAuditAfter) {
                return true;
            }
        }

        return false;
    }

    public function canBeReviewedByUser($userId)
    {
        $reviewerIds = $this->reviews->pluck('reviewer_id')->toArray();

        return $this->isNeedReview && !in_array($userId, $reviewerIds);
    }
}
