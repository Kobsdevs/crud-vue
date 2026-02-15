<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContributionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/contributions', [ContributionController::class, 'store'])->name('contributions.store');
    Route::delete('contributions/{contribution}', [ContributionController::class, 'destroy'])->name('contributions.destroy');
    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
});

require __DIR__.'/settings.php';
