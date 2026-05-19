<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Ticket::with(['user', 'device', 'files'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:250'],
            'user_id' => ['required', 'exists:users,id'],
            'device_id' => ['required', 'exists:devices,id'],
            'status' => ['required', 'integer'],
            'type' => ['required', 'integer'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $ticket = Ticket::create($validated);


        if ($request->hasFile('files')) {
            $files = $request->file('files');

            foreach ($files as $file) {
                // Validación adicional
                $mimeType = $file->getMimeType();
                $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

                if (!in_array($mimeType, $allowedMimes)) {
                    continue; // Saltar archivos no válidos
                }

                // Generar nombre único para el archivo
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Guardar archivo en storage/app/ticket_files
                $path = $file->storeAs('ticket_files', $fileName, 'public');

                // Guardar registro en base de datos
                TicketFile::create([
                    'ticket_id' => $ticket->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return response()->json($ticket, 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json($ticket->load(['user', 'device', 'files']));
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'device_id' => ['sometimes', 'exists:devices,id'],
            'status' => ['sometimes', 'integer', 'max:100'],
            'type' => ['sometimes', 'integer'],
        ]);

        $ticket->update($validated);

        return response()->json($ticket->load(['user', 'device', 'files']));
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        // Eliminar archivos asociados
        foreach ($ticket->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
