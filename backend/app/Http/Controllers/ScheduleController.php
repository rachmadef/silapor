<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Schedule::with(['classRoom', 'creator']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->latest()->get();

        return ScheduleResource::collection($schedules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'type' => 'required|string|in:pickup,return',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'nullable|string|in:active,closed,completed',
        ]);

        $validated['created_by'] = $request->user()->id;

        $schedule = Schedule::create($validated);

        return response()->json([
            'message' => 'Jadwal berhasil dibuat.',
            'data' => new ScheduleResource($schedule->load(['classRoom', 'creator'])),
        ], 201);
    }

    public function show(Schedule $schedule): ScheduleResource
    {
        return new ScheduleResource($schedule->load(['classRoom', 'creator']));
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'type' => 'sometimes|required|string|in:pickup,return',
            'start_at' => 'sometimes|required|date',
            'end_at' => 'sometimes|required|date|after:start_at',
            'status' => 'nullable|string|in:active,closed,completed',
        ]);

        $schedule->update($validated);

        return response()->json([
            'message' => 'Jadwal berhasil diperbarui.',
            'data' => new ScheduleResource($schedule->load(['classRoom', 'creator'])),
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}
