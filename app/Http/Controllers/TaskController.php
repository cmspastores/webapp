<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display the task manager page with all tasks.
     */
    public function showManager()
    {
        $tasks = Task::latest()->get();
        return view('task-manager', compact('tasks'));
    }

    /**
     * Store a new task from the task manager view.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Task::create($validated);

        return redirect()->route('task.manager')->with('success', 'Task created successfully.');
    }

    /**
     * Update an existing task from the task manager view.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->route('task.manager')->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task from the task manager view.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.manager')->with('success', 'Task deleted successfully.');
    }
}
