<?php

namespace App\Http\Controllers;

use App\Http\Resources\LaptopResource;
use App\Models\Laptop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LaptopController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Laptop::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $laptops = $query->latest()->get();

        return LaptopResource::collection($laptops);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|unique:laptops,asset_code|max:100',
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:available,borrowed,maintenance',
            'is_active' => 'boolean',
        ]);

        $laptop = Laptop::create($validated);

        return response()->json([
            'message' => 'Laptop berhasil ditambahkan.',
            'data' => new LaptopResource($laptop),
        ], 201);
    }

    public function show(Laptop $laptop): LaptopResource
    {
        return new LaptopResource($laptop);
    }

    public function update(Request $request, Laptop $laptop): JsonResponse
    {
        $validated = $request->validate([
            'asset_code' => 'sometimes|required|string|max:100|unique:laptops,asset_code,' . $laptop->id,
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:available,borrowed,maintenance',
            'is_active' => 'boolean',
        ]);

        $laptop->update($validated);

        return response()->json([
            'message' => 'Data laptop berhasil diperbarui.',
            'data' => new LaptopResource($laptop),
        ]);
    }

    public function destroy(Laptop $laptop): JsonResponse
    {
        $laptop->delete();

        return response()->json(['message' => 'Data laptop berhasil dihapus.']);
    }
}
