<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CapacitorService
{
    public function getLeave()
    {
        $url = env('CAPACITOR_API_URL');
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-client-id' => env('CAPACITOR_CLIENT_ID'),
            'x-api-key' => env('CAPACITOR_API_KEY')
        ];
        try {
            $response = Http::withHeaders($headers)->get($url . '/leave');
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return response()->json(['error' => 'Error retrieving data from Capacitor API'], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createLeave($payload)
    {

        $rules = [
            'key1' => 'required|string',
            'key2' => 'numeric'
        ];

        $validator = Validator::make($payload, $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $url = env('CAPACITOR_API_URL');
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-client-id' => env('CAPACITOR_CLIENT_ID'),
            'x-api-key' => env('CAPACITOR_API_KEY')
        ];

        try {
            $response = Http::withHeaders($headers)->post($url, $payload);
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                return response()->json(['error' => 'Error posting data to Capacitor API'], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
