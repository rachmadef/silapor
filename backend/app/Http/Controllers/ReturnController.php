<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnScanRequest;
use App\Http\Resources\ReturnResource;
use App\Models\LaptopReturn;
use App\Services\ReturnService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReturnController extends Controller
{
    /**
     * Process return QR code scan.
     */
    public function scan(ReturnScanRequest $request, ReturnService $returnService): JsonResponse
    {
        try {
            $returnRecord = $returnService->processReturn(
                token: $request->qr_token,
                officer: $request->user(),
                condition: $request->input('condition', 'good'),
                notes: $request->notes
            );

            return response()->json([
                'status' => 'success',
                'message' => "Pengembalian laptop untuk {$returnRecord->loan->student->name} berhasil dicatat.",
                'data' => new ReturnResource($returnRecord),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get return history.
     */
    public function history(Request $request): AnonymousResourceCollection
    {
        $query = LaptopReturn::with(['loan.student.classRoom', 'loan.student.parent', 'loan.laptop', 'officer']);

        if ($request->has('date')) {
            $query->whereDate('returned_at', $request->date);
        } else {
            $query->whereDate('returned_at', now()->toDateString());
        }

        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        $returns = $query->latest('returned_at')->get();

        return ReturnResource::collection($returns);
    }
}
