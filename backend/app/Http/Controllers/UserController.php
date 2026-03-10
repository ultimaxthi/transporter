<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    /**
     * Listar usuários
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $users = $query->paginate(15);

        return response()->json($users);
    }

    /**
     * Criar usuário
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'unique:users,email'],
            'password'            => ['required', 'string', 'min:6'],
            'role'                => ['required', 'in:' . implode(',', User::ROLES)],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'active'              => ['boolean'],
        ]);

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'data' => $user
        ], 201);
    }

    /**
     * Exibir usuário
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'                => ['sometimes', 'string', 'max:255'],
            'email'               => ['sometimes', 'email', "unique:users,email,{$user->id}"],
            'password'            => ['nullable', 'string', 'min:6'],
            'role'                => ['sometimes', 'in:' . implode(',', User::ROLES)],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'active'              => ['boolean'],
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'data' => $user
        ]);
    }

    /**
     * Deletar usuário
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuário removido com sucesso.'
        ]);
    }

    /**
     * Listar apenas motoristas
     */
    public function drivers(): JsonResponse
    {
        $drivers = User::drivers()->active()->get();

        return response()->json($drivers);
    }

    /**
     * Listar apenas operadores
     */
    public function operators(): JsonResponse
    {
        $operators = User::operators()->active()->get();

        return response()->json($operators);
    }

    /**
     * Listar apenas administradores
     */
    public function admins(): JsonResponse
    {
        $admins = User::admins()->active()->get();

        return response()->json($admins);
    }
}