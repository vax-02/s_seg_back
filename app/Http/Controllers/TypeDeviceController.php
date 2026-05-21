<?php

namespace App\Http\Controllers;

use App\Models\TypeDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypeDeviceController extends Controller
{
    
    public function index(): JsonResponse
    {
        $devices = TypeDevice::get();
        return response()->json($devices);
    }

    public function store(Request $request): JsonResponse{
        $validated = $request->validate(
            [
                'name' => 'required|string|unique:type_devices,name'
            ]
        );
        if($validated){
            $type = TypeDevice::create(
                $validated
            );
        }
        return response()->json($type,201);
    }
    public function destroy($id): JsonResponse
    {
        $device = TypeDevice::find($id);
        $deleted = $device->delete();
        if (!$deleted) {
            return response()->json([
                'message' => 'No se pudo eliminar el tipo de dispositivo.'
            ], 500);
        }

        return response()->json([
            'message' => 'Tipo de dispositivo eliminado correctamente.'
        ], 200);
    }
    public function update($id, Request $request): JsonResponse
{
    $typeDevice = TypeDevice::find($id);

    if (!$typeDevice) {
        return response()->json([
            'message' => 'Tipo de dispositivo no encontrado.'
        ], 404);
    }

    $validated = $request->validate([
        'name' => 'required|string|unique:type_devices,name,' . $id
    ], [
        'name.unique' => 'Este tipo de dispositivo ya existe.'
    ]);

    $updated = $typeDevice->update($validated);

    if (!$updated) {
        return response()->json([
            'message' => 'No se pudo actualizar el tipo de dispositivo.'
        ], 500);
    }

    return response()->json([
        'message' => 'Tipo de dispositivo actualizado correctamente.',
        'data' => $typeDevice
    ], 200);
}
}
