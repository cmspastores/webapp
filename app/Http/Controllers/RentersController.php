<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;

class RentersController extends Controller
{
    // Display a listing of renters
    public function index()
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5); // pagination 5
        return view('renter-manager', compact('renters'));
    }

    // Show the form for creating a new renter (reuse same Blade)
    public function create()
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5);
        return view('renter-manager', compact('renters'));
    }

    // Store a new renter
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'dob'               => 'nullable|date',
            'email'             => 'required|email|unique:renters,email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        Renters::create($validated);

        return redirect()->route('renters.index')->with('success', 'Renter created successfully.');
    }

    // Edit renter (show edit form in same Blade)
    public function edit(Renters $renter)
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5);
        return view('renter-manager', compact('renters', 'renter'));
    }

    // Update renter
    public function update(Request $request, Renters $renter)
    {
        $validated = $request->validate([
       'first_name' => 'required|string|max:255',
       'last_name'  => 'required|string|max:255',
       'email'      => 'required|email|unique:renters,email,' . $renter->renter_id . ',renter_id',
       'phone'      => 'nullable|string|max:20',
       'dob'        => 'nullable|date',
       'emergency_contact' => 'nullable|string|max:255',
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
