<?php

use Illuminate\Support\Facades\Route;
use App\Models\SensorReading;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/iot-dashboard', function () {
    $latest = SensorReading::latest('id')->first();

    $readings = SensorReading::latest('id')
        ->limit(20)
        ->get();

    return view('iot-dashboard', compact('latest', 'readings'));
});