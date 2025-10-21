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
        return view('agents.show', compact('agent'));
    }
}