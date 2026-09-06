<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Student::with(['classRoom', 'parent', 'qrCode', 'activeLoan']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->get();

        return StudentResource::collection($students);
    }

    public function store(Request $request, QrCodeService $qrCodeService): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis|max:50',
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'parent_id' => 'nullable|exists:parents,id',
            'is_active' => 'boolean',
        ]);

        $student = Student::create($validated);
        $qrCodeService->generateForStudent($student);

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan & QR Code telah digenerate.',
            'data' => new StudentResource($student->load(['classRoom', 'parent', 'qrCode'])),
        ], 201);
    }

    public function show(Student $student): StudentResource
    {
        return new StudentResource($student->load(['classRoom', 'parent', 'qrCode', 'activeLoan']));
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'sometimes|required|string|max:50|unique:students,nis,' . $student->id,
            'name' => 'sometimes|required|string|max:255',
            'class_id' => 'sometimes|required|exists:classes,id',
            'parent_id' => 'nullable|exists:parents,id',
            'is_active' => 'boolean',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => new StudentResource($student->load(['classRoom', 'parent', 'qrCode'])),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json(['message' => 'Data siswa berhasil dihapus.']);
    }

    /**
     * Regenerate QR Code for student.
     */
    public function generateQr(Student $student, QrCodeService $qrCodeService): JsonResponse
    {
        if ($student->qrCode) {
            $student->qrCode()->delete();
        }

        $qrCode = $qrCodeService->generateForStudent($student);

        return response()->json([
            'message' => 'QR Code berhasil diperbarui.',
            'data' => [
                'token' => $qrCode->token,
                'qr_image_url' => $qrCodeService->getQrCodeDataUri($qrCode->token),
            ],
        ]);
    }
}
