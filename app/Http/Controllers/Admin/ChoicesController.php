<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            ->with('children')
            ->orderBy('name')
            ->get();

        return $this->orderTags($records, $default);
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
                'choices' => []
            ];

            foreach ($record->children as $child) {
                $thisChild = [
                    'label' => $child->name,
                    'value' => $child->id
                ];

                if (in_array($child->id, $default)) {
                    $thisChild['selected'] = true;
                }

                $thisArray['choices'][] = $thisChild;
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
