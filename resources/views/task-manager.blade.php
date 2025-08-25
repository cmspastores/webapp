 <x-app-layout>
    <x-slot name="header">
       <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Task Manager
        </h2> -->
        Room Types
    </x-slot> 

    <style>
        .header-bar {
            background-color: #2563eb;
            color: white;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            padding: 24px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border-bottom: 4px solid #0ea5e9;
            margin-bottom: 24px;
        }

        .success-message {
            background-color: #ecfdf5;
            color: #065f46;
            padding: 12px 16px;
            border: 2px solid #10b981;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 15px;
            font-weight: 500;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #94a3b8;
            border-radius: 8px;
            background-color: #ffffffff;
            color: #1e293b;
            font-size: 15px;
        }

        .form-button {
            padding: 10px 18px;
            background: linear-gradient(to right, #0ea5e9, #3b82f6);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-button:hover {
            background: linear-gradient(to right, #0284c7, #2563eb);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            color: #1e293b;
            border: 1px solid #cbd5e1;
        }

        thead {
            background-color: #3b82f6;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        .table-input {
            width: 100%;
            padding: 8px 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #1e293b;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .save-button {
            color: #0f766e;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
        }

        .delete-button {
            color: #dc2626;
            font-weight: bold;
            background: none;
            border: none;
            cursor: pointer;
        }

        .no-tasks {
            color: #64748b;
            margin-top: 20px;
            font-style: italic;
        }
    </style>

    <div class="header-bar">Room Types</div> 

    <div class="py-8 px-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        {{-- 📝 Create Task Form --}}
        <form action="{{ route('tasks.store') }}" method="POST">
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
        @if ($tasks->count())
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
                        @foreach ($tasks as $task)
                            <tr>
                                <form action="{{ route('tasks.update', $task) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <td>
                                        <input type="text" name="title" value="{{ $task->title }}" required class="table-input">
                                    </td>
                                    <td>
                                        <input type="text" name="description" value="{{ $task->description }}" class="table-input">
                                    </td>
                                    <td class="action-buttons">
                                        <button type="submit" class="save-button">💾 Save</button>
                                </form>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');">
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
            <p class="no-roomtypes">No tasks yet.</p>
        @endif
    </div>
</x-app-layout>
