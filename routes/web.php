<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContributionController;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $totalCampaigns = Campaign::count();
    $activeCampaigns = Campaign::where('status', 'active')->count();
    $fundedCampaigns = Campaign::where('status', 'funded')->count();
    $totalContributions = Contribution::count();
    $totalRaised = Contribution::sum('amount');
    $totalGoal = Campaign::sum('goal_amount');
    $totalCategories = Category::count();
    $totalUsers = User::count();

    $recentCampaigns = Campaign::with(['user', 'category'])
        ->withCount('contributions')
        ->latest()
        ->take(5)
        ->get()
        ->map(fn ($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'slug' => $c->slug,
            'goal_amount' => $c->goal_amount,
            'current_amount' => $c->current_amount,
            'status' => $c->status,
            'progress' => $c->progressPercentage(),
            'contributions_count' => $c->contributions_count,
            'user' => ['name' => $c->user->name],
            'category' => $c->category ? ['name' => $c->category->name] : null,
            'created_at' => $c->created_at->diffForHumans(),
        ]);

    $recentContributions = Contribution::with(['user', 'campaign'])
        ->latest()
        ->take(5)
        ->get()
        ->map(fn ($c) => [
            'id' => $c->id,
            'amount' => $c->amount,
            'is_anonymous' => $c->is_anonymous,
            'user' => $c->is_anonymous ? null : ['name' => $c->user->name],
            'campaign' => ['title' => $c->campaign->title],
            'created_at' => $c->created_at->diffForHumans(),
        ]);

    return Inertia::render('Dashboard', [
        'stats' => [
            'totalCampaigns' => $totalCampaigns,
            'activeCampaigns' => $activeCampaigns,
            'fundedCampaigns' => $fundedCampaigns,
            'totalContributions' => $totalContributions,
            'totalRaised' => (float) $totalRaised,
            'totalGoal' => (float) $totalGoal,
            'totalCategories' => $totalCategories,
            'totalUsers' => $totalUsers,
        ],
        'recentCampaigns' => $recentCampaigns,
        'recentContributions' => $recentContributions,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/contributions', [ContributionController::class, 'store'])->name('contributions.store');
    Route::delete('contributions/{contribution}', [ContributionController::class, 'destroy'])->name('contributions.destroy');
    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
});

require __DIR__.'/settings.php';
