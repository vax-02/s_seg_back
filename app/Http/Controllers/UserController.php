<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'integer'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'rol' => $validated['rol'],
        ]);

        return response()->json($user, 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function devices(User $user): JsonResponse
    {
        return response()->json($user->devices()->with('user')->get());
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        if ($user->status != 1) {
            return response()->json(['message' => 'Usuario bloqueado'], 403);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        return response()->json($user);
    }

    public function changePassword(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);
        

        // Validar que el usuario enviado coincida
        if ($validated['user_id'] != $user->id) {
            return response()->json([
                'message' => 'Usuario inválido'
            ], 403);
        }

        // Validar contraseña actual
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Contraseña actual incorrecta'
            ], 403);
        }

        // Guardar nueva contraseña encriptada
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ], 200);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'rol' => ['sometimes', 'integer'],
        ]);

        $user->fill([
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'rol' => $validated['rol'] ?? $user->rol,
        ]);
        $user->save();

        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    private function validationRules(bool $isUpdate = false, ?User $user = null): array
    {
        $emailRule = [
            $isUpdate ? 'sometimes' : 'required',
            'email',
            'max:255',
        ];

        if ($isUpdate && $user) {
            $emailRule[] = Rule::unique('users', 'email')->ignore($user->id);
        } else {
            $emailRule[] = Rule::unique('users', 'email');
        }

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'email' => $emailRule,
            'password' => [$isUpdate ? 'sometimes' : 'required', 'string', 'min:8'],
        ];
    }
    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'integer', 'in:0,1'],
        ]);

        $user->status = $validated['status'];
        $user->save();

        return response()->json($user);
    }

    /**
     * Búsqueda dinámica de usuarios por nombre o email
     * GET /users/search?q=valor
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        // No buscar si el query está vacío
   //     return response()->json([$query]);
        if (empty(trim($query))) {
            return response()->json(['asd']);
        }

        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function myTickets(User $user,Device $device ): JsonResponse
    {
        $tickets = $user->tickets()
            ->where('device_id', $device->id)
            ->with('device:id')->orderBy('created_at', 'desc')
            ->get();
        return response()->json($tickets);
    }
}
