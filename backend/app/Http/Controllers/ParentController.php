<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ParentModel::withCount('students');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $parents = $query->latest()->get();

        return response()->json(['data' => $parents]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:30',
            'is_active' => 'boolean',
        ]);

        $parent = ParentModel::create($validated);

        return response()->json([
            'message' => 'Data wali murid berhasil ditambahkan.',
            'data' => $parent,
        ], 201);
    }

    public function show(ParentModel $parent): JsonResponse
    {
        return response()->json(['data' => $parent->load('students')]);
    }

    public function update(Request $request, ParentModel $parent): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'whatsapp_number' => 'sometimes|required|string|max:30',
            'is_active' => 'boolean',
        ]);

        $parent->update($validated);

        return response()->json([
            'message' => 'Data wali murid berhasil diperbarui.',
            'data' => $parent,
        ]);
    }

    public function destroy(ParentModel $parent): JsonResponse
    {
        $parent->delete();

        return response()->json(['message' => 'Data wali murid berhasil dihapus.']);
    }
}
