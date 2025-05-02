<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $authUser = User::find(Auth::id());
        $role = $authUser->roles;

        $developers = User::whereHas('roles', function ($query) {
            $query->where('name', 'developer');
        })->with('roles')->get();

        $projects = [];
        if ($role[0]->name === 'admin'){
            $projects = Project::all();
        } elseif ($role[0]->name === 'developer'){
            $projects = $authUser->projects;
        }

        foreach ($projects as $project) {
            $project['create_by'] = $project->creator->name;
        }

        return Inertia::render('Project/Index', [
            'projects' => $projects,
            'permissions' => $role[0]->name,
            'developers' => $developers,
        ]);

    }

    public function show($id): Response
    {
        $authUser = Auth::user();
        $role = $authUser->roles->first();
        $permissions = $role ? $role->name : '';

        $project = Project::with('sprints')->findOrFail($id);

        $developers = User::whereHas('roles', function ($query) {
            $query->where('name', 'developer');
        })->with('roles')->get();

        $associatedDevelopers = $project->users;

        return Inertia::render('Project/Show', [
            'project' => $project,
            'developers' => $developers,
            'associatedDevelopers' => $associatedDevelopers,
            'permissions' => $permissions,
            'sprints' => $project->sprints,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'developers_ids' => 'nullable|array',
            'developers_ids.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'create_by' => auth()->id(),
        ]);

        if (isset($validatedData['developers_ids'])) {
            $project->users()->attach($validatedData['developers_ids']);
        }

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Project created successfully');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'developers_ids' => 'nullable|array',
            'developers_ids.*' => 'exists:users,id',
            'status' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($id);

        $project->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
        ]);

        if (isset($validatedData['developers_ids'])) {
            $project->users()->sync($validatedData['developers_ids']);
        }

        return redirect()
            ->route('projects.show', $project->id)
            ->with('message', 'Project updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $project = Project::where('id', $id)->firstOrFail();
        $project->delete();

        return response()->json(['message' => 'Project deleted'], 200);
    }

}
