<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TypeDeviceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserController::class, 'login']);
Route::get('dashboard', [DashboardController::class, 'index']);


Route::get('users/search', [UserController::class, 'search']);
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}/devices', [UserController::class, 'devices']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::put('users/{user}/status', [UserController::class, 'updateStatus']);
Route::get('users/{user}/tickets/{device}/devices', [UserController::class, 'myTickets']); //ticket y estado de user



Route::patch('users/{user}', [UserController::class, 'update']);
Route::post('users/{user}/change-password', [UserController::class, 'changePassword']);
Route::delete('users/{user}', [UserController::class, 'destroy']);

Route::get('devices', [DeviceController::class, 'index']);
Route::post('devices', [DeviceController::class, 'store']);
Route::get('devices/{device}', [DeviceController::class, 'show']);
Route::put('devices/{device}', [DeviceController::class, 'update']);
Route::patch('devices/{device}', [DeviceController::class, 'update']);
Route::delete('devices/{device}', [DeviceController::class, 'destroy']);
Route::get('devices/{device}/assignments-history', [DeviceController::class, 'assignmentsHistory']);
Route::post('devices/{device}/assign', [DeviceController::class, 'assignUser']);
Route::get('devices/my/{id}', [DeviceController::class, 'myDevices']);

Route::get('tickets', [TicketController::class, 'index']);
Route::get('tickets/accepted', [TicketController::class, 'getTicketAccepted']);
Route::patch('/tickets/assign/{ticket}', [TicketController::class, 'assignTicket']);
Route::get('tickets/{ticket}/comments', [TicketController::class, 'comments']);
Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment']);
Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus']);

Route::post('tickets', [TicketController::class, 'store']);
Route::get('tickets/{ticket}', [TicketController::class, 'show']);
Route::put('tickets/{ticket}', [TicketController::class, 'update']);
Route::patch('tickets/{ticket}', [TicketController::class, 'update']);
Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);


Route::get('type-devices', [TypeDeviceController::class, 'index']);
Route::post('type-devices', [TypeDeviceController::class, 'store']);
Route::delete('type-devices/{id}', [TypeDeviceController::class, 'destroy']);
Route::patch('type-devices/{id}', [TypeDeviceController::class, 'update']);

Route::get('users/{user}/tasks', [TaskController::class, 'index']);
Route::get('users/{user}/tasks/historial', [TaskController::class, 'historial']);

