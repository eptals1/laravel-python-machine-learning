<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MLController extends Controller
{
    protected $pythonApiUrl;

    public function __construct()
    {
        $this->pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:5000');
    }

    public function predict(Request $request)
    {
        $request->validate([
            'features' => 'required|array',
        ]);

        try {
            $response = Http::post($this->pythonApiUrl . '/predict', [
                'features' => $request->features
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Failed to get prediction from ML service'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error connecting to ML service: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return view('ml.predict');
    }
}
