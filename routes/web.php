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

Route::get('login/link', [\App\Http\Controllers\Auth\MagicLoginController::class, 'showLoginForm'])->name('login.link');
Route::post('login/link', [\App\Http\Controllers\Auth\MagicLoginController::class, 'sendLoginLink']);
Route::get('login/link/{token}', [\App\Http\Controllers\Auth\MagicLoginController::class, 'login'])->name('login.link.token');

Route::get('sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index' ]);

/**
 * PUBLIC ROUTES
 */
Route::get('/', [App\Http\Controllers\Public\ContentController::class, 'home'])->name('home');
Route::get('/ventures', [App\Http\Controllers\Public\ContentController::class, 'ventures'])->name('public.content.ventures');
Route::get('/terms', [App\Http\Controllers\Public\ContentController::class, 'terms'])->name('public.content.terms');
Route::get('/privacy', [App\Http\Controllers\Public\ContentController::class, 'privacy'])->name('public.content.privacy');
Route::get('/contact', [App\Http\Controllers\Public\ContentController::class, 'contact'])->name('public.content.contact');
Route::get('/about', [App\Http\Controllers\Public\ContentController::class, 'about'])->name('public.content.about');
Route::get('/about/{slug}', [App\Http\Controllers\Public\ContentController::class, 'aboutUser'])->name('public.content.about.user');
Route::get('/confirmed', [App\Http\Controllers\Public\ContentController::class, 'emailConfirmed'])->name('public.content.email-confirmed');
Route::get('/articles', [App\Http\Controllers\Public\ContentController::class, 'articlesIndex'])->name('public.content.articles.index');
Route::get('/articles/{slug}', [App\Http\Controllers\Public\ContentController::class, 'articleShow'])->name('public.content.articles.show');
Route::get('/glossary', [App\Http\Controllers\Public\ContentController::class, 'glossaryIndex'])->name('public.content.glossary.index');
Route::get('/glossary/{slug}', [App\Http\Controllers\Public\ContentController::class, 'glossaryShow'])->name('public.content.glossary.show');
Route::get('/index', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndex'])->name('public.content.longevity-index');
Route::get('/index/about', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndexAbout'])->name('public.content.longevity-index.about');
Route::get('/index/terms-and-disclaimers', [App\Http\Controllers\Public\ContentController::class, 'longevityStockIndexTerms'])->name('public.content.longevity-index.terms');
Route::get('/longevity-stock-chart', [App\Http\Controllers\Public\ContentController::class, 'longevityStockChart'])->name('public.content.longevity-index.chart');

// Companies
Route::get('/companies/', [App\Http\Controllers\Public\Content\Organizations\OrganizationController::class, 'index'])
    ->name('public.content.organizations.index');
Route::get('/companies/charts/organizations-by-type.json', [App\Http\Controllers\Public\Content\Organizations\ChartController::class, 'organizationsByTypePieChartData'])
    ->name('public.content.organizations.charts.organizations-by-type');
Route::get('/companies/charts/organizations-locations-map.json', [App\Http\Controllers\Public\Content\Organizations\ChartController::class, 'organizationLocationsMapData'])
    ->name('public.content.organizations.charts.organizations-locations-map');
Route::get('/companies/charts/organizations-by-founded-date.json', [App\Http\Controllers\Public\Content\Organizations\ChartController::class, 'organizationByFoundedDateChart'])
    ->name('public.content.organizations.charts.organizations-by-founded-date');
Route::get('/companies/{slug}', [App\Http\Controllers\Public\Content\Organizations\OrganizationController::class, 'show'])
    ->name('public.content.organizations.show');
Route::get('/companies/{slug}/stock-chart', [App\Http\Controllers\Public\Content\Organizations\ChartController::class, 'stockChart'])
    ->name('public.content.organizations.chart');
Route::get('/companies/{slug}/stock-data', [App\Http\Controllers\Public\Content\Organizations\ChartController::class, 'stockData'])
    ->name('public.content.organizations.data');

// Investors
Route::get('/investors/', [App\Http\Controllers\Public\Content\Investors\InvestorController::class, 'index'])->name('public.content.investors.index');
Route::get('/investors/charts/investors-by-type.json', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'investorsByTypePieChartData'])
    ->name('public.content.investors.charts.investors-by-type');
Route::get('/investors/charts/total-amount-raised-last-12-months.json', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'totalAmountRaisedLast12Months'])
    ->name('public.content.investors.charts.total-amount-raised-last-12-months');
Route::get('/investors/charts/amount-raised-per-round.json', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'amountRaisedPerRoundType'])
    ->name('public.content.investors.charts.amount-raised-per-round');
Route::get('/investors/charts/average-raised-per-round.json', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'averageRaisedPerRoundType'])
    ->name('public.content.investors.charts.average-raised-per-round');
Route::get('/investors/charts/ytd-amount-raised-per-category.json', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'yearToDateAmountRaisedPerCategory'])
    ->name('public.content.investors.charts.ytd-amount-raised-per-category');
Route::get('/investors/charts/year-over-year-bar-chart-race', [App\Http\Controllers\Public\Content\Investors\ChartController::class, 'yearOverYearBarChartRace'])
    ->name('public.content.investors.charts.year-over-year-bar-chart-race');

Route::get('/ventures/logos', [App\Http\Controllers\Public\ContentController::class, 'ventureLogos'])->name('public.content.ventures.logos');
Route::get('/reports', [App\Http\Controllers\Public\Content\ReportController::class, 'index'])->name('public.content.reports.index');
Route::get('/reports/2022-longevity-funding', [App\Http\Controllers\Public\Content\ReportController::class, 'longevityReport2022'])->name('public.content.reports.longevity-report-2022');
Route::get('/jobs', [App\Http\Controllers\Public\Content\JobsController::class, 'index'])->name('public.content.jobs.index');
Route::get('/jobs/submit/job', [App\Http\Controllers\Public\Content\JobsController::class, 'submit'])->name('public.content.jobs.submit');
Route::get('/jobs/apply/{slug?}', [App\Http\Controllers\Public\Content\JobsController::class, 'apply'])->name('public.content.jobs.apply');
Route::get('/jobs/subscription/confirmed', [App\Http\Controllers\Public\ContentController::class, 'jobPostingSubscriptionConfirmed'])->name('public.content.jobs.subscription.confirmed');
Route::get('/guide-to-longevity', [App\Http\Controllers\Public\Content\LongevityGuideController::class, 'index'])->name('public.content.longevity-guide.index');

Route::get('/checkout/job-posting-subscription', [App\Http\Controllers\Public\CheckoutController::class, 'jobPostingSubscription']);

Route::get('/fo-verify.html', [App\Http\Controllers\Public\AffiliateVerificationController::class, 'flexOffers'])->name('public.affiliate-verification.flex-offers');

//Route::get('/events', [App\Http\Controllers\Public\ContentController::class, 'eventsIndex'])->name('public.content.events.index');
//Route::get('/events/leveraging-longevity-sd', [App\Http\Controllers\Public\ContentController::class, 'leveragingLongevitySanDiego'])->name('public.content.events.leveraging-longevity-sd');
//Route::get('/events/confirmed', [App\Http\Controllers\Public\ContentController::class, 'eventsConfirmed'])->name('public.content.events.confirmed');
//Route::get('/checkout/events', [App\Http\Controllers\Public\CheckoutController::class, 'eventConfirmation'])->name('public.content.checkout.events');

/**
 * ADMIN ROUTES
 */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Home
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('home');

    /* @can edit articles */
    Route::middleware(['can:edit articles'])->group(function() {
        Route::resource('articles', \App\Http\Controllers\Admin\ArticlesController::class);
        Route::get('/articles/media/{id}', [\App\Http\Controllers\Admin\ArticlesController::class, 'media'])->name('articles.media');
    });

    /* @can edit users */
    Route::middleware(['can:edit users'])->group(function() {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/assign-person', [\App\Http\Controllers\Admin\UserController::class, 'assignPerson'])->name('users.assign-person');
    });

    /* @can edit records */
    Route::middleware(['can:edit records'])->group(function() {
        Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);
        Route::resource('investors', \App\Http\Controllers\Admin\InvestorController::class);
        Route::resource('people', \App\Http\Controllers\Admin\PersonController::class);
        Route::resource('fundraising-rounds', \App\Http\Controllers\Admin\FundraisingRoundController::class);
        Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
        Route::resource('job-categories', \App\Http\Controllers\Admin\JobCategoryController::class);
        Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
        Route::resource('content', \App\Http\Controllers\Admin\ContentController::class);
        Route::resource('form-entries', \App\Http\Controllers\Admin\FormEntryController::class);
        Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
        Route::resource('glossary', \App\Http\Controllers\Admin\GlossaryController::class);

        Route::get('/json/tags.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'tags'])->name('json.tags');
        Route::get('/json/childTagsByParentId.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'childTagsByParentId'])->name('json.childTagsByParentId');
        Route::get('/json/articleTags.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'articleTags'])->name('json.articleTags');
        Route::get('/json/organizations.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'organizations'])->name('json.organizations');
        Route::get('/json/investors.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'investors'])->name('json.investors');
        Route::get('/json/people.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'people'])->name('json.people');
        Route::get('/json/peopleWithoutUser.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'peopleWithoutUser'])->name('json.peopleWithoutUser');
        Route::get('/json/jobs.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'jobs'])->name('json.jobs');
        Route::get('/json/events.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'events'])->name('json.events');
        Route::get('/json/fundraising-rounds.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'fundraisingRounds'])->name('json.fundraising-rounds');
        Route::get('/json/users.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'users'])->name('json.users');
        Route::get('/json/authors.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'authors'])->name('json.authors');
        Route::get('/json/content/parents.json', [\App\Http\Controllers\Admin\ChoicesController::class, 'contentParents'])->name('json.content.parents');

        // Organization Routes & Relationships
        Route::get('/organizations/{slug}/stock-data', [\App\Http\Controllers\Admin\OrganizationController::class, 'stockData'])->name('organizations.stock-data');
        Route::get('/organizations/{slug}/stock-chart', [\App\Http\Controllers\Admin\OrganizationController::class, 'stockChart'])->name('organizations.stock-chart');
        Route::post('/organization/investors/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addInvestors'])->name('organizations.add-investors');

        Route::post('/organization/people/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addPeople'])->name('organizations.add-people'); // old person
        Route::post('/organization/{id}/tags/update', [\App\Http\Controllers\Admin\OrganizationController::class, 'updateTags'])->name('organizations.tags.update');

        Route::get('/organizations/{slug}/people', [\App\Http\Controllers\Admin\OrganizationController::class, 'people'])->name('organizations.people');
        Route::post('/organization/people', [\App\Http\Controllers\Admin\OrganizationController::class, 'savePerson'])->name('organizations.save-person');

        Route::prefix('/organizations/stock')->name('organizations.stock.')->group(function () {
            Route::get('/{slug}/add', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'add'])->name('add');
            Route::get('/create', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'create'])->name('create');
            Route::post('/create', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\OrganizationStockController::class, 'destroy'])->name('destroy');
        });

        Route::get('/organizations/financial-statements/index', [\App\Http\Controllers\Admin\OrganizationFinancialStatementsController::class, 'index'])
            ->name('organizations.financial-statements.index');
        Route::get('/organizations/financial-statements/{slug}', [\App\Http\Controllers\Admin\OrganizationFinancialStatementsController::class, 'show'])
            ->name('organizations.financial-statements.show');

        Route::get('/form-entries/resubmit/mailchimp', [\App\Http\Controllers\Admin\FormEntryController::class, 'resubmitMailchimpApplicableForms'])->name('form-entries.resubmit-mailchimp');

        Route::post('/tags/sync', [\App\Http\Controllers\Admin\TagController::class, 'syncEntities'])->name('tags.sync');
        Route::post('/tags/detach', [\App\Http\Controllers\Admin\TagController::class, 'detachFromEntity'])->name('tags.detach');

        // Investor Relationships
        Route::get('/investors/{slug}/people', [\App\Http\Controllers\Admin\InvestorController::class, 'people'])->name('investors.people');
        Route::post('/investor/people', [\App\Http\Controllers\Admin\InvestorController::class, 'savePerson'])->name('investors.save-person');

        Route::post('/investor/create-from-entity', [\App\Http\Controllers\Admin\InvestorController::class, 'createFromEntity'])->name('investors.create-from-entity');

        // Location
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
        Route::get('/locations/attach/{entityType}/{entityId}', [\App\Http\Controllers\Admin\LocationController::class, 'attach'])->name('locations.attach');
        Route::post('/locations/create-from-entity', [\App\Http\Controllers\Admin\LocationController::class, 'createFromEntity'])->name('locations.create-from-entity');

        Route::get('/longevity-index', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'index'])->name('longevity-index');
        Route::get('/longevity-index/snapshot/hourly', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'getHourlySnapshot'])->name('longevity-index.snapshots.hourly');
        Route::get('/longevity-index/snapshot/eod', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'getEndOfDaySnapshot'])->name('longevity-index.snapshots.eod');
        Route::get('/longevity-index/all', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'all'])->name('longevity-index.all');
        Route::get('/longevity-index/chart', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'chart'])->name('longevity-index.chart');
        Route::get('/longevity-index/chart.json', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'chartData'])->name('longevity-index.chart-data');
        Route::get('/longevity-index/{id}/show', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'show'])->name('longevity-index.show');
        Route::get('/longevity-index/{id}/edit', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'edit'])->name('longevity-index.edit');
        Route::get('/longevity-index/{id}/recalculate', [\App\Http\Controllers\Admin\Index\LongevityIndexController::class, 'recalculate'])->name('longevity-index.recalculate');

        Route::get('/longevity-index/stocks/create', [\App\Http\Controllers\Admin\Index\StockController::class, 'create'])->name('longevity-index.stocks.create');
        Route::post('/longevity-index/stocks/create', [\App\Http\Controllers\Admin\Index\StockController::class, 'store'])->name('longevity-index.stocks.store');
        Route::get('/longevity-index/stocks/{id}', [\App\Http\Controllers\Admin\Index\StockController::class, 'edit'])->name('longevity-index.stocks.edit');
        Route::post('/longevity-index/stocks/{id}', [\App\Http\Controllers\Admin\Index\StockController::class, 'update'])->name('longevity-index.stocks.update');

        //Entity Duplicate
        Route::prefix('entity-duplicate')
            ->name('entity-duplicate.')
            ->group(function() {
                Route::get('/', [\App\Http\Controllers\Admin\EntityDuplicateController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\EntityDuplicateController::class, 'merge'])->name('merge');

            Route::get('/getEntityListJson', [\App\Http\Controllers\Admin\EntityDuplicateController::class, 'getEntityListJson'])->name('get-entity-list-json');
            Route::get('/getEntityForm', [\App\Http\Controllers\Admin\EntityDuplicateController::class, 'getEntityForm'])->name('get-entity-form');
        });
    });

    /* @can admin control */
    Route::middleware(['can:admin control'])->group(function () {
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    /* @can recruiting */
    Route::middleware(['can:recruiting'])->group(function() {
        Route::get('/recruiting', [\App\Http\Controllers\Admin\RecruitingController::class, 'index'])->name('recruiting.index');
        Route::get('/recruiting/applications', [\App\Http\Controllers\Admin\RecruitingController::class, 'applications'])->name('recruiting.applications');
    });

    Route::middleware(['can:review records'])
        ->prefix('/entity-reviews')
        ->name('entity-reviews.')
        ->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\EntityReviewsController::class, 'index'])->name('index');
        Route::get('/reviews/{type}', [\App\Http\Controllers\Admin\EntityReviewsController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/approve', [\App\Http\Controllers\Admin\EntityReviewsController::class, 'approveReview'])->name('reviews.approve');
        Route::get('/audits/{type}', [\App\Http\Controllers\Admin\EntityReviewsController::class, 'audits'])->name('audits');
        Route::post('/audits/approve', [\App\Http\Controllers\Admin\EntityReviewsController::class, 'approveAudit'])->name('audits.approve');

        Route::post('/note', [\App\Http\Controllers\Admin\EntityReviewNotesController::class, 'add'])->name('note.add');
        Route::delete('/note/{id}', [\App\Http\Controllers\Admin\EntityReviewNotesController::class, 'delete'])->name('note.delete');
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

    Route::middleware('can:manage own jobs')->group(function () {
        Route::get('/user/jobs/', [\App\Http\Controllers\Users\JobsController::class, 'index'])->name('users.jobs.index');
        Route::post('/user/jobs/{id}/unsubscribe', [\App\Http\Controllers\Users\JobsController::class, 'unsubscribe'])->name('users.jobs.unsubscribe');
    });
});
