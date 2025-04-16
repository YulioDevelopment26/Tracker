<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SprintController extends Controller
{
    public function show($project_id, $sprint_id): Response
    {
        $authUser = User::find(Auth::id());
        $role = $authUser->roles;
        $sprint = Sprint::with('tasks')->findOrFail($sprint_id);
        $tasks = $sprint->tasks;

        return Inertia::render('Sprint/Show', [
            'sprint' => $sprint,
            'tasks' => $tasks,
            'permissions' => $role[0]->name,
            'project_id' => (int) $project_id,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'project_id' => 'required|integer',
        ]);

        $sprint = Sprint::create([
            'name' => $validatedData['name'],
            'goal' => $validatedData['goal'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'project_id' => $validatedData['project_id'],
        ]);

        return redirect()->route('projects.show', $validatedData['project_id'])
            ->with('success', 'Project created successfully');
    }

    public function update(Request $request, $project_id, $sprint_id): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $sprint = Sprint::findOrFail($sprint_id);

        $sprint->update([
           'name' => $validatedData['name'],
           'goal' => $validatedData['goal'],
           'start_date' => $validatedData['start_date'],
           'end_date' => $validatedData['end_date'],
        ]);

        return redirect()->route('sprints.show', ['project' => $project_id, 'sprint' => $sprint->id]);
    }
    public function destroy($proyect_id, $sprint_id): JsonResponse
    {
        $sprint = Sprint::where('id', $sprint_id)->firstOrFail();
        $sprint->delete();

        return response()->json(['message' => 'Sprint deleted'], 200);
    }
}
