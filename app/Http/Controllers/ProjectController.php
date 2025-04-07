<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;
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
        $role = $authUser?->roles()?->get();
        $roleName = $role[0]->name;

        $projects = Project::with('create_by')->get();


        return Inertia::render('Project/Index', [
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'status' => $project->status,
                    'create_by' => $project->creator?->name ?? 'Desconocido',
                ];
            }),

            'userRole' => $roleName,
        ]);
    }

    public function show($id): Response
    {
        $project = Project::with('sprints')->findOrFail($id);

        return Inertia::render('Project/Show', [
            'project' => $project,
        ]);
    }

    public function create(){}

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'developers_ids' => 'nullable|array',
            'developers_ids.*' => 'exists:users,id',
        ]);


        $project = Project::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'create_by' => auth()->id(),
        ]);

        if (!empty($validatedData['developers_ids'])) {
            $project->users()->attach($validatedData['developers_ids']);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully');
    }
    public function edit(){}
    public function update(){}
    public function destroy($id)
    {
        dd("aca entra");
        $project = Project::where('id', $id)->firstOrFail();
        $project->delete();

        return  redirect()->route('projects.index');
    }

}
