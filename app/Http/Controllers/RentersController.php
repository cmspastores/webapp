<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;

class RentersController extends Controller
{
    // Display a listing of renters
    public function index()
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(10);
        return view('renter-manager', compact('renters'));
    }

    // Show the form for creating a new renter (reuse same Blade)
    public function create()
    {
        return view('renter-manager');
    }

    // Store a new renter
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:renters,email',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        Renters::create($validated);

        return redirect()->route('renters.index')->with('success', 'Renter created successfully.');
    }

    // Display a single renter (optional)
    public function show(Renters $renter)
    {
        return view('renter-manager', compact('renter'));
    }

    // Edit renter (reuse same Blade)
    public function edit(Renters $renter)
    {
        return view('renter-manager', compact('renter'));
    }

    // Update renter
    public function update(Request $request, Renters $renter)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:renters,email,' . $renter->renter_id,
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
        ]);

        $renter->update($validated);

        return redirect()->route('renters.index')->with('success', 'Renter updated successfully.');
    }

    // Delete renter
    public function destroy(Renters $renter)
    {
        $renter->delete();
        return redirect()->route('renters.index')->with('success', 'Renter deleted successfully.');
    }
}
