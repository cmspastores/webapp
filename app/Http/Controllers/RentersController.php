<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RentersController extends Controller
{
    // Display a listing of renters
    public function index()
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5);

        // Format timestamps in app timezone for display
        $renters->transform(function ($renter) {
            $renter->created_at_formatted = $renter->created_at
                ? Carbon::parse($renter->created_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A')
                : '';
            $renter->updated_at_formatted = $renter->updated_at
                ? Carbon::parse($renter->updated_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A')
                : '';
            return $renter;
        });

        return view('renter-manager', compact('renters'));
    }

    // Show the form for creating a new renter (reuse same Blade)
    public function create()
    {
        return $this->index(); // Reuse index logic
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

        // Format timestamps
        $renters->transform(function ($r) {
            $r->created_at_formatted = $r->created_at
                ? Carbon::parse($r->created_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A')
                : '';
            $r->updated_at_formatted = $r->updated_at
                ? Carbon::parse($r->updated_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A')
                : '';
            return $r;
        });

        return view('renter-manager', compact('renters', 'renter'));
    }

    // Update renter
    public function update(Request $request, Renters $renter)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:renters,email,' . $renter->renter_id . ',renter_id',
            'phone'             => 'nullable|string|max:20',
            'dob'               => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'address'           => 'nullable|string|max:255',
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
