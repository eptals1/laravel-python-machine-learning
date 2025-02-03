<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ResumeMatcherController extends Controller
{
    protected $pythonApiUrl;

    public function __construct()
    {
        $this->pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:5000');
    }

    public function index()
    {
        return view('resume-matcher');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'resumes.*' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'jobTitle' => 'required|string|max:255',
            'requiredSkills' => 'required|string',
            'experienceYears' => 'required|numeric|min:0',
            'additionalRequirements' => 'nullable|string',
        ]);

        try {
            // Store uploaded resumes
            $resumePaths = [];
            foreach ($request->file('resumes') as $resume) {
                $path = $resume->store('resumes', 'public');
                $resumePaths[] = $path;
            }

            // Prepare job requirements
            $jobRequirements = [
                'title' => $request->jobTitle,
                'skills' => explode(',', $request->requiredSkills),
                'experience' => $request->experienceYears,
                'additional' => $request->additionalRequirements,
            ];

            // Send to Python service for analysis
            $response = Http::post($this->pythonApiUrl . '/analyze-resumes', [
                'resumes' => $resumePaths,
                'requirements' => $jobRequirements,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'matches' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze resumes',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage(),
            ], 500);
        }
    }
}
