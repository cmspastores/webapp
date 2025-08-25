<?php

namespace App\Http\Controllers;

use App\Models\Roomtypes;
use Illuminate\Http\Request;

class RoomtypesController extends Controller
{
    /**
     * Display a list of all tasks (tasks.index route).
     */
    public function index()
    {
        $roomtypes = Roomtypes::latest()->get();
        return view('roomtypes.index', compact('roomtypes'));
    }

    /**
     * Display the task manager page with all tasks.
     */
    public function showManager()
    {
        $roomtypes = Roomtypes::latest()->get();
        return view('Roomtypes-manager', compact('roomtypes'));
    }

    /**
     * Store a new task from the task manager view.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Roomtypes::create($validated);

        return redirect()->route('roomtypes.manager')->with('success', 'Roomtype created successfully.');
    }

    /**
     * Update an existing task from the task manager view.
     */
    public function update(Request $request, Roomtypes $roomtypes)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $roomtypes->update($validated);

        return redirect()->route('roomtypes.manager')->with('success', 'Roomtype updated successfully.');
    }

    /**
     * Delete a task from the task manager view.
     */
    public function destroy(Roomtypes $roomtypes)
    {
        $roomtypes->delete();

        return redirect()->route('roomtypes.manager')->with('success', 'roomtype deleted successfully.');
    }
}
