<?php

namespace App\Http\Controllers\Public\Content\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class OrganizationController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $totalOrganizations = Organization::count();

        $foundedDateCutoff = 2010;
        $foundedAfterValue = Organization::where('founded_date', '>=', $foundedDateCutoff)->count();
        $foundAfterPercent = ($foundedAfterValue / $totalOrganizations) * 100;
        $foundedDifference = Carbon::now()->format('Y') - $foundedDateCutoff;

        $jobsCount = Job::count();
        $jobCategoriesCount = JobCategory::count();
        $topHiringCompanies = Organization::withCount('jobs')->orderByDesc('jobs_count')->take(3)->get();

        $topLocations = Location::withCount('organizations')->orderByDesc('organizations_count')->take(5)->get();

        $seo = Content::where('slug', 'companies')->first();

        $topTags = Tag::whereHas('organizations')->withCount('organizations')->orderByDesc('organizations_count')->take(10)->get()->pluck('organizations_count', 'name')->toArray();

        return view('public.content.organizations.index', [
            'seo' => $seo,
            'topTags' => $topTags,
            'totalOrganizations' => $totalOrganizations,
            'foundedAfterValue' => $foundedAfterValue,
            'foundAfterPercent' => $foundAfterPercent,
            'foundedDifference' => $foundedDifference,
            'jobsCount' => $jobsCount,
            'jobCategoriesCount' => $jobCategoriesCount,
            'topHiringCompanies' => $topHiringCompanies,
            'topLocations' => $topLocations
        ]);
    }

    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $organization = Organization::with(['locations', 'stock', 'stock.latestSnapshot'])
            ->withCount(['jobs', 'mediaItems'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.content.organizations.show', [
           'organization' => $organization
        ]);
    }
}
