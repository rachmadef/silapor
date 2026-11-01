<?php

namespace App\Http\Controllers;

use App\Http\Resources\LaptopResource;
use App\Models\Laptop;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LaptopController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Laptop::with(['student.classRoom']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $laptops = $query->latest()->get();

        return LaptopResource::collection($laptops);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'asset_code' => 'nullable|string|unique:laptops,asset_code|max:100',
            'brand' => 'required|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:available,borrowed,maintenance',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['asset_code'])) {
            $validated['asset_code'] = $this->generateAssetCode($validated['student_id'] ?? null);
        }

        if (! empty($validated['student_id'])) {
            $existing = Laptop::where('student_id', $validated['student_id'])->first();
            if ($existing) {
                $existing->update([
                    'brand' => $validated['brand'],
                    'model' => $validated['model'] ?? null,
                    'serial_number' => $validated['serial_number'] ?? $existing->serial_number,
                    'status' => $validated['status'] ?? $existing->status,
                ]);

                return response()->json([
                    'message' => 'Data laptop siswa berhasil diperbarui.',
                    'data' => new LaptopResource($existing->load(['student.classRoom'])),
                ]);
            }
        }

        $laptop = Laptop::create($validated);

        return response()->json([
            'message' => 'Laptop berhasil ditambahkan.',
            'data' => new LaptopResource($laptop->load(['student.classRoom'])),
        ], 201);
    }

    public function show(Laptop $laptop): LaptopResource
    {
        return new LaptopResource($laptop->load(['student.classRoom']));
    }

    public function update(Request $request, Laptop $laptop): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'asset_code' => 'sometimes|required|string|max:100|unique:laptops,asset_code,' . $laptop->id,
            'brand' => 'sometimes|required|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:available,borrowed,maintenance',
            'is_active' => 'boolean',
        ]);

        $laptop->update($validated);

        return response()->json([
            'message' => 'Data laptop berhasil diperbarui.',
            'data' => new LaptopResource($laptop->load(['student.classRoom'])),
        ]);
    }

    public function destroy(Laptop $laptop): JsonResponse
    {
        $laptop->delete();

        return response()->json(['message' => 'Data laptop berhasil dihapus.']);
    }

    /**
     * Public endpoint: Get active students list for the student laptop registration form.
     */
    public function publicStudents(Request $request): JsonResponse
    {
        $query = Student::with(['classRoom', 'laptop'])->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->get()->map(fn (Student $s) => [
            'id' => $s->id,
            'nis' => $s->nis,
            'name' => $s->name,
            'class_name' => $s->classRoom?->name ?? '-',
            'laptop' => $s->laptop ? [
                'id' => $s->laptop->id,
                'asset_code' => $s->laptop->asset_code,
                'brand' => $s->laptop->brand,
                'model' => $s->laptop->model,
            ] : null,
        ]);

        return response()->json(['data' => $students]);
    }

    /**
     * Public endpoint: Submit or update laptop data by student.
     */
    public function submitStudentForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'brand' => 'required|string|max:100',
            'model' => 'nullable|string|max:100',
        ], [
            'student_id.required' => 'Silakan pilih nama peserta didik terlebih dahulu.',
            'student_id.exists' => 'Data peserta didik tidak valid.',
            'brand.required' => 'Merk laptop wajib diisi.',
        ]);

        $student = Student::with('classRoom')->findOrFail($validated['student_id']);

        $laptop = Laptop::where('student_id', $student->id)->first();

        if ($laptop) {
            $laptop->update([
                'brand' => trim($validated['brand']),
                'model' => isset($validated['model']) && trim($validated['model']) !== '' ? trim($validated['model']) : null,
            ]);
            $message = "Data laptop untuk {$student->name} berhasil diperbarui.";
            $statusCode = 200;
        } else {
            $laptop = Laptop::create([
                'student_id' => $student->id,
                'asset_code' => $this->generateAssetCode($student->id),
                'brand' => trim($validated['brand']),
                'model' => isset($validated['model']) && trim($validated['model']) !== '' ? trim($validated['model']) : null,
                'status' => 'available',
                'is_active' => true,
            ]);
            $message = "Data laptop untuk {$student->name} berhasil didaftarkan.";
            $statusCode = 201;
        }

        return response()->json([
            'message' => $message,
            'data' => new LaptopResource($laptop->load(['student.classRoom'])),
        ], $statusCode);
    }

    private function generateAssetCode(?int $studentId = null): string
    {
        if ($studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $baseCode = 'LAP-' . $student->nis;
                if (! Laptop::where('asset_code', $baseCode)->exists()) {
                    return $baseCode;
                }
            }
        }

        $count = Laptop::count() + 1;
        do {
            $code = sprintf('LAP-%03d', $count);
            $count++;
        } while (Laptop::where('asset_code', $code)->exists());

        return $code;
    }
}
