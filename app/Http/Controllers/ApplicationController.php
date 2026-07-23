<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Services\Dhis2Service;
use App\Services\MetadataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'career_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cover_letter' => 'required|array',
            'resume' => 'required|array',
            'enrollment'=>'required|string|max:255',
        ]);
   if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Handle file upload for resume if provided
        $validatedData = $validator->validated();
       
        /* Handle file upload for resume if provided the file is uploaded as base64 encoded string in the request the file structure is
            {"cover_letter":{"name":"Ticket Moro TO Dodoma.pdf","type":"application/pdf","content":"in base64 format"}
        */
        //work on cover letter and resume upload
        if (isset($validatedData['cover_letter']['content'])) {
            $coverLetterContent = base64_decode($validatedData['cover_letter']['content']);
            $coverLetterName = $validatedData['cover_letter']['name'];
            $coverLetterPath = 'uploads/cover_letters/' . time() . '_' . $coverLetterName;
            file_put_contents(public_path($coverLetterPath), $coverLetterContent);
            $validatedData['cover_letter'] = $coverLetterPath;
        }

        if (isset($validatedData['resume']['content'])) {
            $resumeContent = base64_decode($validatedData['resume']['content']);
            $resumeName = $validatedData['resume']['name'];
            $resumePath = 'uploads/resumes/' . time() . '_' . $resumeName;
            file_put_contents(public_path($resumePath), $resumeContent);
            $validatedData['resume'] = $resumePath;
        }


        $application = Application::create($validatedData);

        /*trackedEntity	"LTrByWpghCe"
createdAt	"2026-06-20T08:09:00.142"
updatedAt	"2026-07-09T10:50:17.296"
orgUnit	"LA93mGekSOL"*/

        $metadata = new MetadataService();
        $dhis2Service = new Dhis2Service($metadata);
        $data = '';
        $data2 = [
            'program' => 'IBuQKAzlpM8',
            'programStage' => 'SP6mQgWvfSN',
            'orgUnit' => 'LA93mGekSOL',
            'enrollment' => $request->input('enrollment'),
            'trackedEntityInstance' => $request->input('career_id'),
            'eventDate' => now()->toDateString(),
            'status' => 'ACTIVE',
            'dataValues' => [
                ['dataElement' => 'OIlHXyQGQom', 'value' => $request->input('name')],
                ['dataElement' => 'kIiHFHVJGa5', 'value' => $request->input('phone')],
                ['dataElement' => 'TjpEmLh0JIP', 'value' => $request->input('email')],
                ['dataElement' => 'sm5B2sWfYRk', 'value' => $validatedData['cover_letter']],
                ['dataElement' => 'jzAMin6gFoZ', 'value' =>  $validatedData['resume']],
                ['dataElement' => 'QD9LRaNQAqV', 'value' => 'Stage1']
            ],
        ];
        $response = $dhis2Service->addEventToEnrollment($data2);

        if ($response->successful()) {
            // Handle success
            $data = $response->json();
        } else {
            // Handle error
            $error = $response->body();
            $payload = ['events' => [$data2]];
            return response()->json(['message' => 'Failed to add event to enrollment', 'error' => $error, 'payload' => $payload], 500);
        }

        return response()->json(['message' => 'Application submitted successfully', 'application' => $application, 'promis' => $data], 201);
    }

    public function someMethod(Dhis2Service $dhis2Service)
    {
        // Add an event
        $response = $dhis2Service->addEventToEnrollment([
            'program' => 'IBuQKAzlpM8',
            'programStage' => 'SP6mQgWvfSN',
            'orgUnit' => 'LA93mGekSOL',
            'enrollment' => 'tW3EOUgVuEy',
            'eventDate' => '2026-07-06',
            'status' => 'ACTIVE',
            'dataValues' => [
                ['dataElement' => 'OIlHXyQGQom', 'value' => 'John Doe'],
                ['dataElement' => 'kIiHFHVJGa5', 'value' => '1234567890'],
                ['dataElement' => 'TjpEmLh0JIP', 'value' => 'john.doe@example.com'],
            ],
        ]);

        if ($response->successful()) {
            // Handle success
            $data = $response->json();
        } else {
            // Handle error
            $error = $response->body();
        }
    }

    public function index()
    {
        $applications = Application::all();
        return response()->json($applications);
    }

    public function destroy($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->delete();

        return response()->json(['message' => 'Application deleted successfully']);
    }

    public function show($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        return response()->json($application);
    }
}
