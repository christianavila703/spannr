<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EntityReviews\ApproveRequest;
use App\Models\EntityReview;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EntityReviewsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $unreviewedEntities = [];
        $reviewedNotApprovedEntities = [];
        $needAuditEntities = [];
        $entityTypes = config('entity-reviews.entity_types');
        $userId = auth()->user()->id;

        foreach ($entityTypes as $entityType => $entityClass) {
            $unreviewedEntities[$entityType] = $entityClass::unreviewedByUserId($userId)->count();
            $reviewedNotApprovedEntities[$entityType] = $entityClass::reviewedNotApprovedByUserId($userId)->count();
            $needAuditEntities[$entityType] = $entityClass::auditRequired()->count();
        }

        return view('admin.entity-reviews.index', [
            'unreviewedEntities' => $unreviewedEntities,
            'reviewedNotApprovedEntities' => $reviewedNotApprovedEntities,
            'needAuditEntities' => $needAuditEntities,
        ]);
    }

    public function reviews($type): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $entityTypes = config('entity-reviews.entity_types');

        if (!isset($entityTypes[$type])) {
            abort(404);
        }

        return view('admin.entity-reviews.reviews', [
            'entityClass' => $entityTypes[$type],
            'formattedType' => Str::plural(ucwords(str_replace('_', ' ', $type)))
        ]);
    }

    public function approveReview(ApproveRequest $request)
    {
        $data = $request->validated();
        $data['reviewer_id'] = auth()->user()->id;

        EntityReview::create($data);

        return redirect()->back()->with('success', 'Entity review was approved.');
    }

    public function audits($type): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $entityTypes = config('entity-reviews.entity_types');

        if (!isset($entityTypes[$type])) {
            abort(404);
        }

        return view('admin.entity-reviews.audits', [
            'entityClass' => $entityTypes[$type],
            'formattedType' => Str::plural(ucwords(str_replace('_', ' ', $type)))
        ]);
    }

    public function approveAudit(ApproveRequest $request)
    {
        $entityId = $request->entity_id;
        $entityType = $request->entity_type;

        try {
            $reviews = EntityReview::where('entity_id', $entityId)
                ->where('entity_type', $entityType)
                ->get();

            foreach ($reviews as $review) {
                $review->updated_at = Carbon::now();
                $review->save();
            }

        } catch (\Throwable $throwable) {
            return redirect()->back()->with('error', $throwable->getMessage());
        }

        return redirect()->back()->with('success', 'Entity audit was approved.');
    }
}
