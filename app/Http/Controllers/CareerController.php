<?php

namespace App\Http\Controllers;

use App\Models\Career;
use App\Services\Dhis2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\MetadataService;

class CareerController extends Controller
{
    public function store(Request $request)
    {
       
        $validatedData =  Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'salary' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            'closing_date' => 'nullable|date',
            'status' => 'required|in:open,closed',
        ]);

          if ($validatedData->fails()) {
            return response()->json(['errors' => $validatedData->errors()], 422);
        }
        $career = Career::create($validatedData->validated());

        return response()->json(['message' => 'Career created successfully', 'career' => $career], 201);
    }

    public function index()
    {
        $metadata = new MetadataService();
        $data = (new Dhis2Service($metadata))->getTrackedEntities();
        return $data['raw']['trackedEntities'];
        $careers = Career::all();
        return response()->json($careers);
    }

    public function show($id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        return response()->json($career);
    }

    public function update(Request $request, $id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'department' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'salary' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'description' => 'sometimes|required|string',
            'responsibilities' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|string',
            'closing_date' => 'nullable|date',
            'status' => 'sometimes|required|in:open,closed',
        ]);

        $career->update($validatedData);

        return response()->json(['message' => 'Career updated successfully', 'career' => $career]);
    }

    public function destroy($id)
    {
        $career = Career::find($id);

        if (!$career) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        $career->delete();

        return response()->json(['message' => 'Career deleted successfully']);
    }
}
