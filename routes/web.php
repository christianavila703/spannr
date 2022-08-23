<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

/**
 * PUBLIC ROUTES
 */
Route::get('/', [App\Http\Controllers\Public\ContentController::class, 'home'])->name('home');
Route::get('/ventures', [App\Http\Controllers\Public\ContentController::class, 'ventures'])->name('public.content.ventures');
Route::get('/terms', [App\Http\Controllers\Public\ContentController::class, 'terms'])->name('public.content.terms');
Route::get('/privacy', [App\Http\Controllers\Public\ContentController::class, 'privacy'])->name('public.content.privacy');
Route::get('/contact', [App\Http\Controllers\Public\ContentController::class, 'contact'])->name('public.content.contact');
Route::get('/confirmed', [App\Http\Controllers\Public\ContentController::class, 'emailConfirmed'])->name('public.content.email-confirmed');
Route::get('/articles', [App\Http\Controllers\Public\ContentController::class, 'articlesIndex'])->name('public.content.articles.index');
Route::get('/articles/{slug}', [App\Http\Controllers\Public\ContentController::class, 'articleShow'])->name('public.content.articles.show');
Route::get('/glossary', [App\Http\Controllers\Public\ContentController::class, 'glossaryIndex'])->name('public.content.glossary.index');
Route::get('/glossary/{slug}', [App\Http\Controllers\Public\ContentController::class, 'glossaryShow'])->name('public.content.glossary.show');
Route::get('/index', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndex'])->name('public.content.longevity-index');
Route::get('/index/about', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndexAbout'])->name('public.content.longevity-index.about');
Route::get('/index/terms-and-disclaimers', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndexTerms'])->name('public.content.longevity-index.terms');
Route::get('/longevity-stock-chart', [App\Http\Controllers\Public\ContentController::class, 'longevityStockChart'])->name('public.content.longevity-index.chart');
Route::get('/companies/', [App\Http\Controllers\Public\ContentController::class, 'companiesList'])->name('public.content.organizations.index');
Route::get('/companies/{slug}', [App\Http\Controllers\Public\ContentController::class, 'organizationShow'])->name('public.content.organizations.show');
Route::get('/companies/{slug}/stock-chart', [App\Http\Controllers\Public\ContentController::class, 'organizationChart'])->name('public.content.organizations.chart');
Route::get('/companies/{slug}/stock-data', [App\Http\Controllers\Public\ContentController::class, 'organizationChartData'])->name('public.content.organizations.data');
Route::get('/leveraging-longevity', [App\Http\Controllers\Public\ContentController::class, 'leveragingLongevityEvent'])->name('public.content.events.leveraging-longevity');

/**
 * ADMIN ROUTES
 */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    /* @can edit records */
    Route::middleware(['can:edit records'])->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('home');
        Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);
        Route::resource('investors', \App\Http\Controllers\Admin\InvestorController::class);
        Route::resource('people', \App\Http\Controllers\Admin\PersonController::class);
        Route::resource('fundraising-rounds', \App\Http\Controllers\Admin\FundraisingRoundController::class);
        Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
        Route::resource('job-categories', \App\Http\Controllers\Admin\JobCategoryController::class);
        Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('content', \App\Http\Controllers\Admin\ContentController::class);
        Route::resource('form-entries', \App\Http\Controllers\Admin\FormEntryController::class);
        Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
        Route::get('/json/tags.json', [\App\Http\Controllers\Admin\TagController::class, 'tagsList'])->name('json.tags');

        // Organization Routes & Relationships
        Route::get('/organizations/{slug}/stock-data', [\App\Http\Controllers\Admin\OrganizationController::class, 'stockData'])->name('organizations.stock-data');
        Route::get('/organizations/{slug}/stock-chart', [\App\Http\Controllers\Admin\OrganizationController::class, 'stockChart'])->name('organizations.stock-chart');
        Route::post('/organization/investors/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addInvestors'])->name('organizations.add-investors');

        Route::post('/organization/people/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addPeople'])->name('organizations.add-people'); // old person

        Route::get('/organizations/{slug}/people', [\App\Http\Controllers\Admin\OrganizationController::class, 'people'])->name('organizations.people');
        Route::post('/organization/people', [\App\Http\Controllers\Admin\OrganizationController::class, 'savePerson'])->name('organizations.save-person');

        Route::post('/tags/sync', [\App\Http\Controllers\Admin\TagController::class, 'syncEntities'])->name('tags.sync');

        // Investor Relationships
        Route::get('/investors/{slug}/people', [\App\Http\Controllers\Admin\InvestorController::class, 'people'])->name('investors.people');
        Route::post('/investor/people', [\App\Http\Controllers\Admin\InvestorController::class, 'savePerson'])->name('investors.save-person');

        // Location
        Route::get('/locations/{entityType}/{entityId}', [\App\Http\Controllers\Admin\LocationController::class, 'attach'])->name('locations.attach');
        Route::post('/locations/attach', [\App\Http\Controllers\Admin\LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{id}', [\App\Http\Controllers\Admin\LocationController::class, 'show'])->name('locations.show');

        Route::get('/longevity-index', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'index'])->name('longevity-index');
        Route::get('/longevity-index/update', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'updateIndex'])->name('longevity-index.update');
        Route::get('/longevity-index/all', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'all'])->name('longevity-index.all');
        Route::get('/longevity-index/chart', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'chart'])->name('longevity-index.chart');
        Route::get('/longevity-index/chart.json', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'chartData'])->name('longevity-index.chart-data');
        Route::get('/longevity-index/{id}', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'show'])->name('longevity-index.show');

        // Articles
        Route::get('/articles/sync', [\App\Http\Controllers\Admin\ArticlesController::class, 'sync'])->name('articles.sync');
    });

    // PERMISSION: Admin Control
    Route::middleware(['can:admin control'])->group(function () {
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
    });
});

/**
 * LOGGED-IN USER ROUTES
 */
Route::middleware('auth')->group(function () {
    // User Settings
    Route::get('/settings', [\App\Http\Controllers\Users\SettingsController::class, 'index'])->name('users.settings');
    Route::post('/settings', [\App\Http\Controllers\Users\PasswordController::class, 'store'])->name('users.store-password');
    Route::post('/settings/api', [\App\Http\Controllers\Users\ApiController::class, 'createKey'])->name('users.api.create');
    Route::post('/settings/api/delete', [\App\Http\Controllers\Users\ApiController::class, 'deleteKey'])->name('users.api.delete');
});
