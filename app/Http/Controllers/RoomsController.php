<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // eager load roomType to avoid N+1 queries
        $rooms = Room::with('roomType')->get();
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roomTypes = RoomType::all(); // for dropdown
        return view('rooms.create', compact('roomTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number'          => 'required|string|max:50|unique:rooms,room_number',
            'room_type_id'         => 'nullable|exists:room_types,id',
            'room_price'           => 'required|numeric|min:0',
            'number_of_occupants'  => 'nullable|integer|min:0',
            'image1'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image3'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $room = new Room();
        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'] ?? null;
        $room->room_price = $validated['room_price'];
        $room->number_of_occupants = $validated['number_of_occupants'] ?? null;

        foreach (['image1', 'image2', 'image3'] as $field) {
            if ($request->hasFile($field)) {
                $room->$field = $request->file($field)->store('rooms', 'public');
            }
        }

        $room->save();

        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::all(); // for dropdown
        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number'          => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'room_type_id'         => 'nullable|exists:room_types,id',
            'room_price'           => 'required|numeric|min:0',
            'number_of_occupants'  => 'nullable|integer|min:0',
            'image1'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image3'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        foreach (['image1', 'image2', 'image3'] as $field) {
            // Handle removal request
            if ($request->filled("remove_$field") && $request->input("remove_$field") === "1") {
                if ($room->$field) {
                    Storage::disk('public')->delete($room->$field);
                }
                $room->$field = null; // clear DB column safely
            }

            // Handle new upload
            if ($request->hasFile($field)) {
                // delete old file if exists
                if ($room->$field) {
                    Storage::disk('public')->delete($room->$field);
                }
                $room->$field = $request->file($field)->store('rooms', 'public');
            }
        }

        // Update only the validated non-image fields
        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'] ?? null;
        $room->room_price = $validated['room_price'];
        $room->number_of_occupants = $validated['number_of_occupants'] ?? null;

        $room->save();

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        foreach (['image1', 'image2', 'image3'] as $field) {
            if ($room->$field) {
                Storage::disk('public')->delete($room->$field);
            }
        }

        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}