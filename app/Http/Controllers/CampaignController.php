<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    public function index(Request $request): Response
    {
        $campaigns = Campaign::with(['user', 'category'])
            ->withCount('contributions')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->input('category'), function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('campaigns/Index', [
            'campaigns' => $campaigns,
            'categories' => Category::orderBy('name')->get(),
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('campaigns/Create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(CampaignRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        Campaign::create($data);

        return to_route('campaigns.index')
            ->with('success', 'Campagne créée avec succès.');
    }

    public function show(Campaign $campaign): Response
    {
        $campaign->load(['user', 'category', 'contributions' => function ($query) {
            $query->with('user')->latest()->limit(10);
        }]);

        return Inertia::render('campaigns/Show', [
            'campaign' => $campaign,
            'progressPercentage' => $campaign->progressPercentage(),
            'contributionsCount' => $campaign->contributions()->count(),
        ]);
    }

    public function edit(Campaign $campaign): Response
    {
        return Inertia::render('campaigns/Edit', [
            'campaign' => $campaign,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(CampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($data);

        return to_route('campaigns.show', $campaign)
            ->with('success', 'Campagne mise à jour avec succès.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return to_route('campaigns.index')
            ->with('success', 'Campagne supprimée avec succès.');
    }
}
