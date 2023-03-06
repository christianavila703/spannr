<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EntityReviews\EntityReviewNoteRequest;
use App\Models\EntityReviewNote;

class EntityReviewNotesController extends Controller
{
    public function add(EntityReviewNoteRequest $request)
    {
        $data = $request->validated();
        $data['reviewer_id'] = auth()->user()->id;

        try {
            EntityReviewNote::updateOrCreate([
                'entity_id' => $data['entity_id'],
                'entity_type' => $data['entity_type'],
            ], $data);
        } catch (\Throwable $throwable) {
            return redirect()->back()->with('error', $throwable->getMessage());
        }

        return redirect()->back()->with('success', 'Review note was added!');
    }

    public function delete($reviewNoteId)
    {
        EntityReviewNote::destroy($reviewNoteId);

        return redirect()->back()->with('success', 'Review note was deleted!');
    }
}
