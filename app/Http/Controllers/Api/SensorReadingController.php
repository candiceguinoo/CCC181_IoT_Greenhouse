<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorReadingController extends Controller
{
    public function store(Request $request)
    {
        $expectedToken = (string) env('IOT_DEVICE_TOKEN', '');
        $providedToken = (string) $request->bearerToken();

        if (
            $expectedToken === '' ||
            !hash_equals($expectedToken, $providedToken)
        ) {
            return response()->json([
                'message' => 'Unauthorized device.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:50',
            'temperature' => 'required|numeric|between:-40,85',
            'humidity' => 'required|numeric|between:0,100',
            'recorded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reading = SensorReading::create(
            $validator->validated()
        );

        return response()->json([
            'message' => 'Sensor reading saved successfully.',
            'data' => $reading,
        ], 201);
    }

    public function index()
    {
        return response()->json(
            SensorReading::latest('id')->limit(20)->get()
        );
    }

    public function latest()
    {
        $reading = SensorReading::latest('id')->first();

        if (!$reading) {
            return response()->json([
                'message' => 'No sensor readings found.'
            ], 404);
        }

        return response()->json($reading);
    }
}
