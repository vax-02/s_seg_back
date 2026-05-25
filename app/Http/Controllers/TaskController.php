<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(User $user): JsonResponse
    {
        //5 estado derivado
        //6 estado resuelto
        $tasks = Ticket::where('assigned_user_id', $user->id)
            ->where('status','!=',5)
            ->where('status','!=',6)
            ->with(['device','files','user'])->get();
        return response()->json($tasks);
    }
    public function historial(User $user)
    {    
        if($user->id != 1) {//admin
            $tasks = Ticket::where('assigned_user_id', $user->id)
                ->where('status','=',6)
                ->with(['device','files','user','logs.user'])->get();    
        }else{
            $tasks = Ticket::with(['device','files','user','logs.user'])->get();
        }
        return response()->json($tasks);
    }
}
