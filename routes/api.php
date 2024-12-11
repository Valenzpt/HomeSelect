<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\TaskController;

Route::resource('apartments', ApartmentController::class);
Route::resource('bookings', BookingController::class);
Route::resource('incidents', IncidentController::class);
Route::resource('tasks', TaskController::class);
