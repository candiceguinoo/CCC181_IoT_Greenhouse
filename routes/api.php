<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorReadingController;

Route::post('/iot/readings', [
    SensorReadingController::class, 'store'
]);

Route::get('/iot/readings', [
    SensorReadingController::class, 'index'
]);

Route::get('/iot/readings/latest', [
    SensorReadingController::class, 'latest'
]);
