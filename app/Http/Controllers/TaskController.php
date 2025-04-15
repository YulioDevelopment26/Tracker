<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{

    public function show($id): JsonResponse
    {
        $task = Task::with('user')->findOrFail($id);
        return response()->json($task);
    }
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'priority' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'story_points' => 'required|integer|max:255',
            'sprint_id' => 'required|integer|max:255',
            'project_id' => 'required|integer|max:255',
            'estimated_hours' => 'required|integer|max:255',
        ]);

        $task = Task::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'priority' => $validatedData['priority'],
            'category' => $validatedData['category'],
            'story_points' => $validatedData['story_points'],
            'sprint_id' => $validatedData['sprint_id'],
            'estimated_hours' => $validatedData['estimated_hours'],
        ]);
        return redirect()->route('sprints.show', [
            'project' => $request['project_id'],
            'sprint' => $validatedData['sprint_id'],
        ])->with('success', 'Project created successfully');
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'estimated_start' => 'nullable|date',
            'estimated_finish' => 'nullable|date',
            'status' => 'required|string|max:255',
            'actual_hours' => 'nullable|integer|max:255',
            'story_points' => 'nullable|integer|max:255',
            'user_id' => 'nullable|integer|max:255',
        ]);

        $task = Task::findOrFail($id);

        if ($validatedData['status'] === 'in progress'){
            $task->status = 'in progress';
            $task->actual_start = Carbon::now()->format('d-m-Y');
        }

        if ($validatedData['status'] === 'done'){
            $task->status = 'done';
            $task->actual_finish = Carbon::now()->format('d-m-Y');
        }

        $task->update([
            'estimated_start' => $validatedData['estimated_start'],
            'estimated_finish' => $validatedData['estimated_finish'],
            'status' => $validatedData['status'],
            'actual_start' => $task->actual_start,
            'actual_end' => $task->actual_end,
            'actual_hours' => $validatedData['actual_hours'],
            'story_points' => $validatedData['story_points'],
            'user_id' => $validatedData['user_id'],
        ]);

        return response()->json(['message' => 'Task updated'], 201);
    }
}
