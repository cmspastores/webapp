<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of room types.
     */
    public function index()
    {
        $roomTypes = RoomType::paginate(10);
        return view('roomtypes.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new room type.
     */
    public function create()
    {
        return view('roomtypes.create');
    }

    /**
     * Store a newly created room type in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name',
        ]);

        RoomType::create($request->only('name'));

        return redirect()->route('roomtypes.index')->with('success', 'Room type created successfully.');
    }

    /**
     * Show the form for editing the specified room type.
     */
    public function edit(RoomType $roomtype)
    {
        return view('roomtypes.edit', compact('roomtype'));
    }

    /**
     * Update the specified room type in storage.
     */
    public function update(Request $request, RoomType $roomtype)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_types,name,' . $roomtype->id,
        ]);

        $roomtype->update($request->only('name'));

        return redirect()->route('roomtypes.index')->with('success', 'Room type updated successfully.');
    }

    /**
     * Remove the specified room type from storage.
     */
    public function destroy(RoomType $roomtype)
    {
        $roomtype->delete();

        return redirect()->route('roomtypes.index')->with('success', 'Room type deleted successfully.');
    }
}