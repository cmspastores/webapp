<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomsController extends Controller
{
    private function authorizeAdmin()
    {
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $query = Room::with('roomType');

        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // ✅ Paginate 10 per page with query string
        $rooms = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $roomTypes = RoomType::all();

        return view('rooms.index', compact('rooms', 'roomTypes'))
            ->with('search', $request->search)
            ->with('selectedRoomType', $request->room_type_id);
    }

    public function create()
    {
        $this->authorizeAdmin();
        $roomTypes = RoomType::all();
        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'room_type_id' => 'nullable|exists:room_types,id',
            'room_price' => 'required|numeric|min:0',
            'number_of_occupants' => 'nullable|integer|min:0',
            'image1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $room = new Room();
        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'] ?? null;
        $room->room_price = $validated['room_price'];
        $room->number_of_occupants = $validated['number_of_occupants'] ?? null;

        foreach (['image1','image2','image3'] as $field) {
            if ($request->hasFile($field)) {
                $room->$field = $request->file($field)->store('rooms','public');
            }
        }

        $room->save();

        return redirect()->route('rooms.index')->with('success','Room created successfully.');
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();
        return view('rooms.edit', compact('room','roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'nullable|exists:room_types,id',
            'room_price' => 'required|numeric|min:0',
            'number_of_occupants' => 'nullable|integer|min:0',
            'image1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        foreach (['image1','image2','image3'] as $field) {
            if ($request->filled("remove_$field") && $request->input("remove_$field") === "1") {
                if ($room->$field) {
                    Storage::disk('public')->delete($room->$field);
                }
                $room->$field = null;
            }
            if ($request->hasFile($field)) {
                if ($room->$field) {
                    Storage::disk('public')->delete($room->$field);
                }
                $room->$field = $request->file($field)->store('rooms','public');
            }
        }

        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'] ?? null;
        $room->room_price = $validated['room_price'];
        $room->number_of_occupants = $validated['number_of_occupants'] ?? null;

        $room->save();

        return redirect()->route('rooms.index')->with('success','Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $this->authorizeAdmin();

        foreach (['image1','image2','image3'] as $field) {
            if ($room->$field) Storage::disk('public')->delete($room->$field);
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success','Room deleted successfully.');
    }
}
