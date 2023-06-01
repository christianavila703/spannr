<?php

namespace App\Http\Controllers\Public\Content\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function stockChart($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        return view('public.content.organizations.chart', [
            'slug' => $organization->slug
        ]);
    }

    public function stockData($slug): string
    {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        $chartData = json_decode($organization->stock->data, true);

        $chartLabels = [];
        $valuesClose = [];
        $valuesOpen = [];
        $valuesHigh = [];
        $valuesLow = [];

        foreach ($chartData as $data) {
            $chartLabels[] = Carbon::createFromTimestampMsUTC($data['date'])->format('M d, Y');

            $valuesClose[] = [
                'x' => $data['date'],
                'y' => $data['close']
            ];

            $valuesOpen[] = [
                'x' => $data['date'],
                'y' => $data['open']
            ];

            $valuesHigh[] = [
                'x' => $data['date'],
                'y' => $data['high']
            ];

            $valuesLow[] = [
                'x' => $data['date'],
                'y' => $data['low']
            ];
        }

        return json_encode([
            'chartLabels' => $chartLabels,
            'valuesClose' => $valuesClose,
            'valuesOpen' => $valuesOpen,
            'valuesHigh' => $valuesHigh,
            'valuesLow' => $valuesLow
        ], true);
    }

    public function organizationsByTypePieChartData(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $organizationTypes = Organization::pluck('type', 'id')->toArray();
        $organizationTypeCounts = array_count_values($organizationTypes);

        $data = [];

        $otherCount = 0;

        $namedTypes = [
            Organization::TYPE_PRIVATE,
            Organization::TYPE_PUBLIC,
            Organization::TYPE_NONPROFIT,
            Organization::TYPE_EDUCATIONAL
        ];

        foreach ($organizationTypeCounts as $type => $count) {

            if (in_array($type, $namedTypes)) {
                $data[] = [
                    'category' => $type,
                    'value' => $count,
                    'color' => $this->getTypeColor($type)
                ];
            } else {
                $otherCount =+ $count;
            }
        }

        $data[] = [
            'category' => 'Other',
            'value' => $otherCount,
            'color' => $this->getTypeColor('Other')
        ];

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function organizationLocationsMapData(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if ($request->input('type')) {
             $organizations = Organization::where('type', $request->input('type'))->whereHas('locations')->with('locations')->get();
        } else {
            $organizations = Organization::whereHas('locations')->with('locations')->get();
        }

        $data = [];

        foreach ($organizations as $organization) {
            $firstLocation = $organization->locations->first();

            if ($firstLocation) {

                $data[] = [
                    'title' => $organization->name,
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $firstLocation->longitude,
                            $firstLocation->latitude
                        ]
                    ],
                    'url' => $organization->public_url,
                    'bulletSettings' => [
                        'fill' => $this->getTypeColor($organization->type)
                    ],
                    'type' => $organization->type
                ];
            }
        }

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function organizationByFoundedDateChart() {
        $organizations = Organization::whereNotNull('founded_date')->orderBy('founded_date')->pluck('founded_date')->toArray();
        $foundedDateArray = array_count_values($organizations);

        /* Way 1 */
//        $data = [];
//
//        foreach ($foundedDateArray as $year => $count) {
//            $data[] = [
//                'label' => ' founded in ' . $year,
//                'value' => $count,
//                'date' => Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d')
//            ];
//        }

        /* Way 2 */
        $data = [];
        $beforeYear = 1980;
        $beforeCount = 0;

        foreach ($foundedDateArray as $year => $count) {
            if ($year < $beforeYear) {
                $beforeCount += $count;
            } else {
                $data[] = [
                    'label' => ' founded in ' . $year,
                    'value' => $count,
                    'date' => Carbon::createFromFormat('Y', $year)->startOfYear()->format('Y-m-d')
                ];
            }
        }

        array_unshift($data, [
            'label' => ' founded before ' . $beforeYear,
            'value' => $beforeCount,
            'date' => Carbon::createFromFormat('Y', $beforeYear - 10)->startOfYear()->format('Y-m-d')
        ]);

        return response(json_encode($data, JSON_PRETTY_PRINT));
    }

    private function getTypeColor($type): string
    {
        return match($type) {
            Organization::TYPE_PUBLIC => '#ed008c', // Magenta
            Organization::TYPE_PRIVATE =>'#0ac7fe', // Blue
            Organization::TYPE_EDUCATIONAL =>'#88f200', // Lime
            Organization::TYPE_NONPROFIT =>'#5625ca', // Purple
            default => '#fe8c0c' // Orange
        };
    }
}
