<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Ticket::with(['user', 'device'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'device_id' => ['required', 'exists:devices,id'],
            'status' => ['required', 'string', 'max:100'],
        ]);

        $ticket = Ticket::create($validated);

        return response()->json($ticket->load(['user', 'device']), 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json($ticket->load(['user', 'device']));
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'device_id' => ['sometimes', 'exists:devices,id'],
            'status' => ['sometimes', 'string', 'max:100'],
        ]);

        $ticket->update($validated);

        return response()->json($ticket->load(['user', 'device']));
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
