<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Task Manager') }}
        </h2>
    </x-slot> 

    <style>
        .success-message{background:#FFF9F5;color:#2C2C2C;padding:12px 16px;border:2px solid #E6A574;border-radius:8px;margin-bottom:24px;}
        .form-group{margin-bottom:16px;}
        .form-label{display:block;margin-bottom:6px;color:#5C3A21;font-size:15px;font-weight:500;}
        .form-input,.form-textarea{width:100%;padding:10px 14px;border:1px solid #E6A574;border-radius:8px;background:#FFFDFB;color:#2C2C2C;font-size:15px;}
        .form-button{padding:10px 18px;background:linear-gradient(to right,#D98348,#E6A574);border:none;color:#FFF;font-weight:600;font-size:14px;border-radius:8px;cursor:pointer;}
        .form-button:hover{background:linear-gradient(to right,#C46A35,#D98348);}
        table{width:100%;border-collapse:collapse;margin-top:20px;background:#FFFDFB;color:#2C2C2C;border:1px solid #E6A574;}
        thead{background:#D98348;color:#FFF;}
        th,td{padding:12px;border:1px solid #E6A574;text-align:left;}
        .table-input{width:100%;padding:8px 10px;background:#FFF9F5;border:1px solid #E6A574;border-radius:6px;color:#2C2C2C;}
        .action-buttons{display:flex;gap:10px;}
        .save-button{color:#2E7D32;font-weight:bold;background:none;border:none;cursor:pointer;}
        .delete-button{color:#C2410C;font-weight:bold;background:none;border:none;cursor:pointer;}
        .no-roomtypes{color:#8B5E3C;margin-top:20px;font-style:italic;}
    </style>

    <div class="py-8 px-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        {{-- Create Task Form --}}
        <form action="{{ route('roomtypes.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" required class="form-input">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-textarea"></textarea>
            </div>

            <button type="submit" class="form-button">✅ Confirm</button>
        </form>

        {{-- 📋 Tasks Table --}}
        @if ($roomtypes->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roomtypes as $roomtypes)
                            <tr>
                                <form action="{{ route('roomtypes.update', $roomtypes) }}" method="POST">
                                    @csrf
                                    @method('PUT')
<<<<<<< HEAD
                                    <td>
                                        <input type="text" name="title" value="{{ $roomtypes->title }}" required class="table-input">
                                    </td>
                                    <td>
                                        <input type="text" name="description" value="{{ $roomtypes->description }}" class="table-input">
                                    </td>
=======
                                    <td><input type="text" name="title" value="{{ $roomtypes->title }}" required class="table-input"></td>
                                    <td><input type="text" name="description" value="{{ $roomtypes->description }}" class="table-input"></td>
>>>>>>> 87780e9e574982e816c803ddc8071db391955004
                                    <td class="action-buttons">
                                        <button type="submit" class="save-button">💾 Save</button>
                                </form>
                                <form action="{{ route('roomtypes.destroy', $roomtypes) }}" method="POST" onsubmit="return confirm('Delete this roomtype?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-button">🗑️ Delete</button>
                                </form>
                                    </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-roomtypes">No roomtypes yet.</p>
        @endif
    </div>
</x-app-layout>
