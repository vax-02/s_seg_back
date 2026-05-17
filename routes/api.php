<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [UserController::class, 'login']);

Route::get('users/search', [UserController::class, 'search']);
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}/devices', [UserController::class, 'devices']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::put('users/{user}/status', [UserController::class, 'updateStatus']);

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
Route::post('tickets', [TicketController::class, 'store']);
Route::get('tickets/{ticket}', [TicketController::class, 'show']);
Route::put('tickets/{ticket}', [TicketController::class, 'update']);
Route::patch('tickets/{ticket}', [TicketController::class, 'update']);
Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);
