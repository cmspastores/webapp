<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RentersController extends Controller
{
    /**
     * Display a listing of renters with optional search.
     */
    public function index(Request $request)
    {
        $query = Renters::query();

        // Search filter (name, email, phone)
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $renters = $query->orderBy('created_at', 'desc')->paginate(5);

        // Add formatted dates for display
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

    /**
     * Show the form for creating a new renter.
     * Reuses the index view but highlights the form.
     */
    public function create(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Store a newly created renter.
     */
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

    /**
     * Show the form for editing a renter.
     */
    public function edit(Renters $renter, Request $request)
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5);

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

    /**
     * Update the specified renter.
     */
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

    /**
     * Remove the specified renter.
     */
    public function destroy(Renters $renter)
    {
        $renter->delete();

        return redirect()->route('renters.index')->with('success', 'Renter deleted successfully.');
    }
}
