<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;

class RentersController extends Controller
{
    /**
     * Display a paginated list of renters with optional search/filter.
     */
    public function index(Request $request)
    {
        $query = Renters::query();

        // 🔍 Search across multiple fields
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('unique_id', 'like', "%{$search}%");
            });
        }

        // ✅ Paginate 10 per page and preserve search/filter queries
        $renters = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('renters.index', compact('renters'));
    }

    /**
     * Show form to create a new renter.
     */
    public function create()
    {
        return view('renters.create');
    }

    /**
     * Store a new renter in the database.
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
            'guardian_name'     => 'nullable|string|max:255',
            'guardian_phone'    => 'nullable|string|max:20',
            'guardian_email'    => 'nullable|email|max:255',
        ]);

        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['unique_id'] = strtoupper(bin2hex(random_bytes(4)));

        Renters::create($validated);

        return redirect()->route('renters.index')->with('success', 'Renter created successfully.');
    }

    /**
     * Display a specific renter.
     */
    public function show(Renters $renter)
    {
        return view('renters.view', compact('renter'));
    }

    /**
     * Show form to edit an existing renter.
     */
    public function edit(Renters $renter)
    {
        return view('renters.edit', compact('renter'));
    }

    /**
     * Update a renter's information in the database.
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
            'guardian_name'     => 'nullable|string|max:255',
            'guardian_phone'    => 'nullable|string|max:20',
            'guardian_email'    => 'nullable|email|max:255',
        ]);

        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $renter->update($validated);

        return redirect()->route('renters.index')->with('success', 'Renter updated successfully.');
    }

    /**
     * Delete a renter from the database.
     */
    public function destroy(Renters $renter)
    {
        $renter->delete();

        return redirect()->route('renters.index')->with('success', 'Renter deleted successfully.');
    }

    public function deleted(Request $request)
    {
        $query = Renters::onlyTrashed(); // get soft-deleted only

        if ($request->has('q')) {
            $q = $request->q;
            $filter = $request->filter ?? 'all';

            $query->where(function ($subQuery) use ($q, $filter) {
                if ($filter === 'name') {
                    $subQuery->where('full_name', 'like', "%$q%");
                } elseif ($filter === 'email') {
                    $subQuery->where('email', 'like', "%$q%");
                } elseif ($filter === 'phone') {
                    $subQuery->where('phone', 'like', "%$q%");
                } else {
                    $subQuery->where('full_name', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%")
                            ->orWhere('phone', 'like', "%$q%");
                }
            });
        }

        $renters = $query->orderBy('deleted_at', 'desc')->paginate(10)->withQueryString();

        return view('renters.deleted', compact('renters'));

        
    }
    public function restore($id)
    {
        $renter = Renters::onlyTrashed()->findOrFail($id);
        $renter->restore();

        return redirect()->route('renters.deleted')->with('success', 'Renter restored successfully.');
    }
}
