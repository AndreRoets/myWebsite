<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgentAdminController extends Controller
{
    /**
     * Display a listing of the agents.
     */
    public function index()
    {
        $agents = Agent::latest()->paginate(10);
        return view('admin.agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new agent.
     */
    public function create()
    {
        return view('admin.agents.create', [
            'agent' => new Agent()
        ]);
    }

    /**
     * Store a newly created agent in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'email'       => 'required|email|unique:agents,email',
            'phone'       => 'required|string|max:20',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('agents', 'public');
        }

        Agent::create($validated);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent created successfully.');
    }

    /**
     * Show the form for editing the specified agent.
     */
    public function edit(Agent $agent)
    {
        return view('admin.agents.edit', compact('agent'));
    }

    /**
     * Update the specified agent in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'email'       => 'required|email|unique:agents,email,' . $agent->id,
            'phone'       => 'required|string|max:20',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($agent->image) {
                Storage::disk('public')->delete($agent->image);
            }
            $validated['image'] = $request->file('image')->store('agents', 'public');
        }

        $agent->update($validated);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent updated successfully.');
    }

    /**
     * Remove the specified agent from storage.
     */
    public function destroy(Agent $agent)
    {
        // Delete the image file from storage
        if ($agent->image) {
            Storage::disk('public')->delete($agent->image);
        }

        $agent->delete();

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent deleted successfully.');
    }
}