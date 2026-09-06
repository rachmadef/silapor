<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class NotificationController extends Controller
{
    /**
     * Get WhatsApp notification logs history.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::with(['student.classRoom', 'parent', 'loan.laptop']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->get();

        return response()->json(['data' => $notifications]);
    }

    /**
     * Trigger manual check for overdue laptops.
     */
    public function triggerCheckOverdue(): JsonResponse
    {
        Artisan::call('laptops:check-overdue');
        $output = Artisan::output();

        return response()->json([
            'message' => 'Pemeriksaan transaksi overdue berhasil dijalankan.',
            'output' => trim($output),
        ]);
    }

    /**
     * Retry sending failed WhatsApp notification.
     */
    public function retry(Notification $notification, WhatsAppService $whatsAppService): JsonResponse
    {
        if (! $notification->student || ! $notification->parent || ! $notification->loan) {
            return response()->json([
                'message' => 'Data relasi siswa / wali / transaksi loan tidak ditemukan.',
            ], 422);
        }

        $newNotification = $whatsAppService->sendOverdueNotification(
            $notification->student,
            $notification->parent,
            $notification->loan
        );

        return response()->json([
            'message' => 'Notifikasi WhatsApp berhasil dikirim ulang.',
            'data' => $newNotification,
        ]);
    }
}
