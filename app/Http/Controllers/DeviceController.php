<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function index(): JsonResponse
    {
        $devices = Device::with('currentAssignment.user')->get();
        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'type_device_id' => ['required', 'exists:type_devices,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
        ]);

        $device = Device::create($validated);

        $device->refresh();
        return response()->json($device, 201);
    }
    public function show(Device $device): JsonResponse
    {
        $device->currentAssignment()->load('user'); // Cargar el usuario asignado actual

        return response()->json($device);
    }

    public function update(Request $request, Device $device): JsonResponse
    {
        // Validar permitiendo actualización del code con validación de unicidad
        $validated = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('devices', 'code')->ignore($device->id),
            ],
            'type' => ['sometimes', 'string', 'max:100'],
            'brand' => ['sometimes', 'string', 'max:100'],
            'model' => ['sometimes', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:100'],
        ]);

        $device->update($validated);

        return response()->json($device);
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();

        return response()->json(['message' => 'Device deleted successfully']);
    }

    public function assignmentsHistory(Device $device): JsonResponse
    {
        $history = $device->assignments()->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($history);
    }
   
    public function assignUser(Request $request, Device $device): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        // Buscar asignación activa actual
        $currentAssignment = $device->currentAssignment;

        // Si existe una asignación activa, desactivarla
        if ($currentAssignment) {
            $currentAssignment->update([
                'status' => 0,
            ]);
        }
        // Crear nueva asignación activa
        $device->assignments()->create([
            'user_id' => $validated['user_id'],
        ]);
            
        $device->update([
            'status' => 2, // Cambiar el estado del dispositivo a "Asignado"
        ]);

        return response()->json([
            'message' => 'Device assigned successfully'
        ], 200);
    }
    public function myDevices($id)
    {
        $devices = Device::whereHas('currentAssignment', function ($query) use ($id) {
            $query->where('user_id', $id)->where('status', 1); // Solo asignaciones activas
        })->get();

        return response()->json($devices);
    }
}
