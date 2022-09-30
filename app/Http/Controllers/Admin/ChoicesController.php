<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Person;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class ChoicesController extends Controller
{
    public function tags(): string
    {
        $records = Tag::orderBy('name')->get();
        return $this->getJsonFromModel($records);
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
