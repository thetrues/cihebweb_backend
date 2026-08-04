<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::where('is_active', true)->get();
        return response()->json($programs);
    }

    public function allPrograms()
    {
        $programs = Program::all();
        return response()->json($programs);
    }

    public function show($id)
    {
        $program = Program::find($id);
        if ($program) {
            return response()->json($program);
        } else {
            return response()->json(['message' => 'Program not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $data =  Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
        ]);

        if ($data->fails()) {
            return response()->json(['errors' => $data->errors()], 422);
        }

        $program = Program::create($data->validated());
        return response()->json(['message' => 'Program created successfully', 'data' => $program], 201);
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'start_date' => 'nullable|date',
        ]);

        $program->update($data);
        return response()->json(['message' => 'Program updated successfully', 'data' => $program], 200);
    }

    public function destroy($id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json(['message' => 'Program not found'], 404);
        }

        $program->delete();
        return response()->json(['message' => 'Program deleted successfully'], 200);
    }
}
