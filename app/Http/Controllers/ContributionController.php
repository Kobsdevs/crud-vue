<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributionRequest;
use App\Models\Campaign;
use App\Models\Contribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContributionController extends Controller
{
    public function index(Request $request): Response
    {
        $contributions = Contribution::with(['campaign', 'user'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('contributions/Index', [
            'contributions' => $contributions,
        ]);
    }

    public function store(ContributionRequest $request, Campaign $campaign): RedirectResponse
    {
        if ($campaign->status !== 'active') {
            return back()->withErrors(['campaign' => 'Cette campagne n\'accepte plus de contributions.']);
        }

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['campaign_id'] = $campaign->id;

        Contribution::create($data);

        $campaign->increment('current_amount', $data['amount']);

        if ($campaign->current_amount >= $campaign->goal_amount) {
            $campaign->update(['status' => 'funded']);
        }

        return to_route('campaigns.show', $campaign)
            ->with('success', 'Merci pour votre contribution !');
    }

    public function destroy(Contribution $contribution): RedirectResponse
    {
        $campaign = $contribution->campaign;
        $campaign->decrement('current_amount', $contribution->amount);

        if ($campaign->status === 'funded' && $campaign->current_amount < $campaign->goal_amount) {
            $campaign->update(['status' => 'active']);
        }

        $contribution->delete();

        return back()->with('success', 'Contribution annulée.');
    }
}
