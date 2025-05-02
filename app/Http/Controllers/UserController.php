<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;


class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('roles')->withTrashed()->get();

        return Inertia::render('User/Index', ['users' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'hour_value' => 'required|integer|min:0',
            'work_time' => 'required|string|min:0',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'nickname' => $validatedData['nickname'],
            'email' => $validatedData['email'],
            'password' => Hash::make('developer123*'),
            'hour_value' => $validatedData['hour_value'],
            'work_time' => $validatedData['work_time'],
        ]);

        $user->roles()->attach(2);

        return response()->json(['message' => 'User created'], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'hour_value' => 'required|integer',
            'status' => 'required|string|max:255',
            'work_time' => 'required|string|max:255',
        ]);

        $user = User::withTrashed()->where('id', $id)->first();

        if (trim($validated['password']) !== '') {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            $validated['password'] = $user->password;
        }

        if ($validated['status'] == 'active' && $user->status == 'inactive') {
            $validated['status'] = 'active';
            $user->restore();

        } elseif ($validated['status'] == 'inactive' && $user->status == 'active') {
            $validated['status'] = 'inactive';
        }

        $user->update([
            'name' => $validated['name'],
            'nickname' => $validated['nickname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'hour_value' => $validated['hour_value'],
            'status' => $validated['status'],
            'work_time' => $validated['work_time'],
        ]);

        if ($user->status == 'inactive' && $validated['status'] == 'inactive') {
            $this->destroy($id);
        }

        return response()->json(['message' => 'User updated'], 201);
    }

    public function destroy($id): void
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->delete();

    }

    public function role(): JsonResponse
    {
        $authUser = User::find(Auth::id());
        $role = $authUser->roles;

        return response()->json(['role' => $role[0]->name]);
    }
}
