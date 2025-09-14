<?php

namespace App\Http\Controllers;

use App\Models\Renters;
use Illuminate\Http\Request;

/**
 * Class RentersController
 *
 * Handles CRUD operations for renters in the Dormitel Management System.
 * Features include search, pagination, creating, editing, and updating renter records.
 *
 * @package App\Http\Controllers
 */
class RentersController extends Controller
{
    /**
     * Display a listing of renters with optional search filter.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Renters::query();

        // 🔍 Apply search filter (matches name, email, phone, or unique_id)
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

        // 📑 Get renters list with newest first + paginate (5 per page)
        $renters = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('renter-manager', compact('renters'));
    }

    /**
     * Show the form for creating a new renter.
     *
     * (Reuses the index view with renter form included.)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Store a newly created renter in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ✅ Validate renter input
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'dob'               => 'nullable|date',
            'email'             => 'required|email|unique:renters,email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        // ⚡ Auto-generate full_name + unique_id for renter record
        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['unique_id'] = strtoupper(bin2hex(random_bytes(4))); // Example: "D3F4A2B1"

        Renters::create($validated);

        return redirect()->route('renters.index')->with('success', 'Renter created successfully.');
    }

    /**
     * Display a single renter’s details.
     *
     * @param Renters $renter
     * @return \Illuminate\View\View
     */
    public function show(Renters $renter)
    {
        return view('renters.show', compact('renter'));
    }

    /**
     * Show the form for editing a renter.
     *
     * (Also fetches renters list to keep the manager view consistent.)
     *
     * @param Renters $renter
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function edit(Renters $renter, Request $request)
    {
        $renters = Renters::orderBy('created_at', 'desc')->paginate(5);

        return view('renter-manager', compact('renters', 'renter'));
    }

    /**
     * Update an existing renter in the database.
     *
     * @param Request $request
     * @param Renters $renter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Renters $renter)
    {
        // ✅ Validate renter input
        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:renters,email,' . $renter->renter_id . ',renter_id',
            'phone'             => 'nullable|string|max:20',
            'dob'               => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'address'           => 'nullable|string|max:255',
        ]);

        // ⚡ Keep full_name updated automatically
        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $renter->update($validated);

        return redirect()->route('renters.index')->with('success', 'Renter updated successfully.');
    }
}
