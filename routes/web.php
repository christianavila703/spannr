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
    Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationController::class);
    Route::resource('investors', \App\Http\Controllers\Admin\InvestorController::class);
    Route::resource('people', \App\Http\Controllers\Admin\PersonController::class);
    Route::resource('fundraising-rounds', \App\Http\Controllers\Admin\FundraisingRoundController::class);

    // Organization Relationships
    Route::post('/organizations/investors/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addInvestors'])->name('organizations.add-investors');
    Route::post('/organizations/people/add', [\App\Http\Controllers\Admin\OrganizationController::class, 'addPeople'])->name('organizations.add-people');
});
