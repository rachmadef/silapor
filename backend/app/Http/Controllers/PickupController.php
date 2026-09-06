<?php

namespace App\Http\Controllers;

use App\Http\Requests\PickupScanRequest;
use App\Http\Resources\LoanResource;
use App\Models\LaptopLoan;
use App\Services\PickupService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PickupController extends Controller
{
    /**
     * Process pickup QR code scan.
     */
    public function scan(PickupScanRequest $request, PickupService $pickupService): JsonResponse
    {
        try {
            $loan = $pickupService->processPickup(
                token: $request->qr_token,
                officer: $request->user(),
                laptopAssetCode: $request->laptop_asset_code
            );

            return response()->json([
                'status' => 'success',
                'message' => "Pengambilan laptop untuk {$loan->student->name} berhasil dicatat.",
                'data' => new LoanResource($loan),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get pickup loans history.
     */
    public function history(Request $request): AnonymousResourceCollection
    {
        $query = LaptopLoan::with(['student.classRoom', 'student.parent', 'laptop', 'officer', 'returnRecord']);

        if ($request->has('date')) {
            $query->whereDate('taken_at', $request->date);
        } else {
            // Default to today
            $query->whereDate('taken_at', now()->toDateString());
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('class_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $loans = $query->latest('taken_at')->get();

        return LoanResource::collection($loans);
    }
}
