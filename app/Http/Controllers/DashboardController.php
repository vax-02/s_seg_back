<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  

public function index()
{
    $ticketsByMonth = Ticket::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $months = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    $formattedMonths = $ticketsByMonth->map(function ($item) use ($months) {

        return [
            'month' => $months[$item->month],
            'total' => $item->total,
        ];

    });

    return response()->json([

        'stats' => [

            'total_maintenances' => Ticket::count(),

            'open_tickets' => Ticket::where('status', 2)->count(),

            'resolved_tickets' => Ticket::where('status', 6)->count(),

        ],

        'latest_tickets' => Ticket::with([
                'user',
                'device'
            ])
            ->latest()
            ->take(5)
            ->get(),

        'latest_maintenances' => Ticket::with([
                'device'
            ])
            ->latest()
            ->take(5)
            ->get(),

        'top_devices' => Ticket::selectRaw('device_id, COUNT(*) as total')
            ->with('device')
            ->groupBy('device_id')
            ->orderByDesc('total')
            ->take(5)
            ->get(),

        // NUEVO
        'tickets_by_month' => $formattedMonths,

    ]);
}
}
