<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ChoicesController extends Controller
{
    public function tags(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Tag::whereNull('parent_id')
            ->whereNot('name', Tag::TAG_PARENT_ARTICLE_CATEGORIES)
            ->with('children')
            ->orderBy('name')
            ->get();

        return $this->orderTags($records, $default);
    }

    public function childTagsByParentId(Request $request): string
    {
        $options = [];
        $parentId = $request->input('parent_id');

        if ($parentId) {
            $tags = Tag::where('parent_id', $parentId)
                ->orderBy('name')
                ->get();

            foreach ($tags as $tag) {
                $options[] = [
                    'label' => $tag->name,
                    'value' => $tag->id,
                ];
            }
        }

        return json_encode($options);
    }

    public function articleTags(Request $request): string
    {
        $data = [];
        $default = explode(',', $request->input('default')) ?? [];
        $rootTag = Tag::where('name', Tag::TAG_PARENT_ARTICLE_CATEGORIES)->first();

        if ($rootTag) {
            $tags = Tag::where('parent_id', $rootTag->id)->orderBy('name')->get();

            foreach ($tags as $tag) {
                $thisTag = [
                    'label' => $tag->name,
                    'value' => $tag->id
                ];

                if (in_array($tag->id, $default)) {
                    $thisTag['selected'] = true;
                }

                $data[] = $thisTag;
            }
        }

        return json_encode($data);
    }

    public function authors(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = User::orderBy('name')
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_AUTHOR);
            })->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function organizations(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Organization::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function investors(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Investor::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function people(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Person::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function jobs(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Job::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function events(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Event::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function fundraisingRounds(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = FundraisingRound::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function users(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = User::orderBy('name')->get();
        return $this->getJsonFromModel($records, $default);
    }

    public function contentParents(Request $request): string
    {
        $default = explode(',', $request->input('default')) ?? [];
        $records = Content::orderBy('name')->whereNull('parent_id')->get();

        return $this->getJsonFromModel($records, $default);
    }

    private function getJsonFromModel($records, $default = []): string
    {
        $recordsList = [];

        foreach ($records as $record) {
            $thisArray = [
                'label' => $record->name,
                'value' => $record->id
            ];

            if (in_array($record->id, $default)) {
                $thisArray['selected'] = true;
            }

            $recordsList[] = $thisArray;
        }

        return json_encode($recordsList);
    }

    private function orderTags($records, $default = []): string
    {
        $recordsList = [];

        foreach ($records as $record) {

            $thisArray = [
                'label' => $record->name,
                'id' => $record->id,
                'choices' => [
                    [
                        'label' => $record->name,
                        'value' => $record->id,
                        'selected' => in_array($record->id, $default)
                    ]
                ],
            ];

            foreach ($record->children as $child) {
                $thisArray['choices'][] = [
                    'label' => $child->name,
                    'value' => $child->id,
                    'selected' => in_array($child->id, $default),
                ];
            }

            $recordsList[] = $thisArray;
        }

        return json_encode($recordsList);
    }

    private function getJsonFromModelConst($modelConst, $default): string
    {
        $recordsList = [];

        foreach ($modelConst as $priority) {
            $thisArray = [
                'label' => $priority,
                'value' => $priority
            ];

            if ($default === $priority) {
                $thisArray['selected'] = true;
            }

            $recordsList[] = $thisArray;
        }

        return json_encode($recordsList);
    }
}
