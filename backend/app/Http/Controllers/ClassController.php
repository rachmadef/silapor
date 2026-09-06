<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ClassRoom::withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $classes = $query->latest()->get();

        return response()->json(['data' => $classes]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $classRoom = ClassRoom::create($validated);

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan.',
            'data' => $classRoom,
        ], 201);
    }

    public function show(ClassRoom $classRoom): JsonResponse
    {
        return response()->json(['data' => $classRoom->load('students')]);
    }

    public function update(Request $request, ClassRoom $classRoom): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'grade' => 'sometimes|required|string|max:50',
            'academic_year' => 'sometimes|required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $classRoom->update($validated);

        return response()->json([
            'message' => 'Data kelas berhasil diperbarui.',
            'data' => $classRoom,
        ]);
    }

    public function destroy(ClassRoom $classRoom): JsonResponse
    {
        $classRoom->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }
}
