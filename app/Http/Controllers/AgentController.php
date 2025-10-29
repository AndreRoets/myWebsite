<?php

namespace App\Http\Controllers;

use App\Models\Agent;

class AgentController extends Controller
{
    /**
     * Display a listing of the agents.
     */
    public function index()
    {
        $agents = Agent::all(); // Or use pagination: Agent::paginate(10);
        return view('agents.index', compact('agents'));
    }

    /**
     * Display the specified agent.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\View\View
     */
    public function show(Agent $agent)
    {
        // Load all active properties for the agent.
        // The view will handle the visibility logic (blurring, etc.).
        $agent->load(['properties' => function ($query) {
            $query->whereIn('status', ['for_sale', 'for_rent'])
                  ->latest();
        }]);

        return view('agents.show', compact('agent'));
    }
}