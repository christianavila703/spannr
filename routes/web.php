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

Auth::routes(['register' => false]);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // TODO: Setup @can permissions for view vs edit record permissions
    Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);
    Route::resource('investors', \App\Http\Controllers\Admin\InvestorController::class);
    Route::resource('people', \App\Http\Controllers\Admin\PersonController::class);
    Route::resource('fundraising-rounds', \App\Http\Controllers\Admin\FundraisingRoundController::class);
    Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);

    // Organization Relationships
    Route::post('/organization/investors/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addInvestors'])->name('organizations.add-investors');
    Route::post('/organization/people/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addPeople'])->name('organizations.add-people');

    // Location
    Route::get('/locations/{entityType}/{entityId}', [\App\Http\Controllers\Admin\LocationController::class, 'attach'])->name('locations.attach');
    Route::post('/locations/attach', [\App\Http\Controllers\Admin\LocationController::class, 'store'])->name('locations.store');
    Route::get('/locations/{id}', [\App\Http\Controllers\Admin\LocationController::class, 'show'])->name('locations.show');

    // User Settings
    Route::get('/settings', [\App\Http\Controllers\Users\PasswordController::class, 'index'])->name('users.change-password');
    Route::post('/settings', [\App\Http\Controllers\Users\PasswordController::class, 'store'])->name('users.store-password');

    Route::get('/longevity-index', [\App\Http\Controllers\Index\LongevityIndexController::class, 'index'])->name('longevity-index');
    Route::get('/longevity-index/update', [\App\Http\Controllers\Index\LongevityIndexController::class, 'updateIndex'])->name('longevity-index.update');
    Route::get('/longevity-index/all', [\App\Http\Controllers\Index\LongevityIndexController::class, 'all'])->name('longevity-index.all');
    Route::get('/longevity-index/chart', [\App\Http\Controllers\Index\LongevityIndexController::class, 'chart'])->name('longevity-index.chart');
    Route::get('/longevity-index/chart.json', [\App\Http\Controllers\Index\LongevityIndexController::class, 'chartData'])->name('longevity-index.chart-data');
    Route::get('/longevity-index/{id}', [\App\Http\Controllers\Index\LongevityIndexController::class, 'show'])->name('longevity-index.show');
});

Route::middleware(['auth', 'can:admin control'])->group(function () {
    // Activity Log
    Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
});
