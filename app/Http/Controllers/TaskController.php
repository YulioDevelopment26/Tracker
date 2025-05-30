<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $authUser = User::find(Auth::id());
        $role = $authUser->roles;


        $permissions = $role[0]->name == 'admin' ? 'admin' : 'developer';

        $projects = null;
        if ($role[0]->name == 'admin') {
            $projects = Project::with('sprints.tasks.user')->get();
        } else if ($role[0]->name == 'developer') {
            $projects = Auth::user()->projects()->with([
                'sprints.tasks' => function ($query) {
                    $query->where('user_id', Auth::id());
                },
                'sprints.tasks.user'
            ])->get();
        }

        return Inertia::render('Dashboard', [
            'projects' => $projects,
            'permissions' => $permissions,
        ]);
    }

    public function show($id): JsonResponse
    {
        $task = Task::with('user')->findOrFail($id);
        return response()->json($task);
    }
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
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

    public function update(Request $request, $id): RedirectResponse
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

        if ($validatedData['estimated_start'] && $validatedData['estimated_finish'] && $validatedData['status'] === 'to do'){
            $task->status = 'in progress';
            $task->actual_start = Carbon::now()->format('d-m-Y');
        }

        if ($validatedData['estimated_start'] && $validatedData['estimated_finish'] && $validatedData['status'] === 'in progress'){
            $task->status = 'done';
            $task->actual_finish = Carbon::now()->format('d-m-Y');
        }

        $task->update([
            'estimated_start' => $validatedData['estimated_start'],
            'estimated_finish' => $validatedData['estimated_finish'],
            'status' => $task->status,
            'actual_start' => $task->actual_start,
            'actual_end' => $task->actual_end,
            'actual_hours' => $validatedData['actual_hours'],
            'story_points' => $validatedData['story_points'],
            'user_id' => $validatedData['user_id'],
        ]);
        return redirect()->back()->with('message', 'Task updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully!']);
    }

    public function cancel(int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        $task->update([
            'estimated_start' => null,
            'estimated_finish' => null,
            'status' => 'to do',
            'actual_start' => null,
            'actual_finish' => null,
            'actual_hours' => null,
            'story_points' => 0,
            'user_id' => null,
        ]);

        return response()->json(['message' => 'Task cancelled successfully!']);
    }
}
