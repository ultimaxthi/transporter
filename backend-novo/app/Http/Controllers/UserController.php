<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $users = $query->latest()->paginate(15);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'data'    => $user
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['vehicles', 'trips']);

        return response()->json($user);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'                => ['sometimes', 'string', 'max:255'],
            'email'               => ['sometimes', 'email', "unique:users,email,{$user->id}"],
            'role'                => ['sometimes', 'in:admin,operator,driver'],
            'registration_number' => ['nullable', 'string', "unique:users,registration_number,{$user->id}"],
            'active'              => ['sometimes', 'boolean'],
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'data'    => $user->fresh()
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->update(['active' => false]);

        return response()->json([
            'message' => 'Usuário desativado com sucesso.'
        ]);
    }
}