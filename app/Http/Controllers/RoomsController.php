<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RoomsController extends Controller
{
    private function authorizeAdmin()
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }
    }

    // 🔹 Updated index() method with numeric room_number sorting
    public function index(Request $request)
    {
        $query = Room::with('roomType');

        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

            // ✅ Sort numerically by room_number
    $roomOrder = $request->filled('room_order') ? $request->room_order : 'asc';
    $query->orderByRaw('CAST(room_number AS UNSIGNED) ' . $roomOrder);

    $rooms = $query->paginate(10)->withQueryString();

    $roomTypes = RoomType::all();

    return view('rooms.index', compact('rooms', 'roomTypes'));


        // Paginate 10 per page with query string
        $rooms = $query->paginate(10)->withQueryString();

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
            'room_price' => 'required|numeric|min:0.01',
            'number_of_occupants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $room = new Room();
        $room->room_number = $validated['room_number'];
        $room->room_type_id = $validated['room_type_id'] ?? null;
        $room->room_price = $validated['room_price'];
        $room->number_of_occupants = $validated['number_of_occupants'] ?? null;

        if ($request->hasFile('image')) {
            $room->image = $request->file('image')->store('rooms', 'public');
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
            'room_price' => 'required|numeric|min:0.01',
            'number_of_occupants' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image removal
        if ($request->filled('remove_image') && $request->input('remove_image') === "1") {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $room->image = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $room->image = $request->file('image')->store('rooms', 'public');
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

        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }
        $room->delete();

        return redirect()->route('rooms.index')->with('success','Room deleted successfully.');
    }
}