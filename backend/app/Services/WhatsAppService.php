<?php

namespace App\Services;

use App\Models\LaptopLoan;
use App\Models\Notification;
use App\Models\ParentModel;
use App\Models\Student;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format phone number to Indonesian international standard (628xxx).
     */
    public function formatPhoneNumber(string $number): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Generate standard overdue notification text.
     */
    public function buildOverdueMessage(Student $student, LaptopLoan $loan): string
    {
        $studentName = $student->name;
        $className = $student->classRoom?->name ?? '-';
        $takenTime = $loan->taken_at ? Carbon::parse($loan->taken_at)->format('H:i, d M Y') : 'hari ini';

        return "Yth. Bapak/Ibu Wali dari {$studentName} (Kelas {$className}), kami menginformasikan bahwa laptop sekolah yang diambil pada {$takenTime} belum dikembalikan hingga batas waktu pukul 15.00 WIB. Mohon untuk mengingatkan putra/putri Bapak/Ibu untuk segera mengembalikan laptop tersebut ke petugas lab. Terima kasih.";
    }

    /**
     * Send WhatsApp notification to parent.
     */
    public function sendOverdueNotification(Student $student, ParentModel $parent, LaptopLoan $loan): Notification
    {
        // 1. Create or get existing PENDING notification record
        $notification = Notification::create([
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'loan_id' => $loan->id,
            'type' => 'OVERDUE_WARNING',
            'status' => 'PENDING',
        ]);

        $recipientNumber = $this->formatPhoneNumber($parent->whatsapp_number);
        $message = $this->buildOverdueMessage($student, $loan);

        try {
            $gatewayUrl = config('services.whatsapp.url', env('WA_GATEWAY_URL'));
            $apiKey = config('services.whatsapp.key', env('WA_API_KEY'));

            if ($gatewayUrl && $apiKey) {
                // Send via external HTTP WA Gateway API
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->post($gatewayUrl, [
                    'target' => $recipientNumber,
                    'message' => $message,
                ]);

                if ($response->successful()) {
                    $notification->update([
                        'status' => 'SENT',
                        'sent_at' => Carbon::now(),
                    ]);
                } else {
                    $notification->update([
                        'status' => 'FAILED',
                        'error_message' => 'HTTP Gateway Error: ' . $response->body(),
                    ]);
                }
            } else {
                // Log-based mock sending for local dev/testing
                Log::info("[WhatsApp Service] Overdue alert sent to {$recipientNumber} ({$parent->name}): {$message}");

                $notification->update([
                    'status' => 'SENT',
                    'sent_at' => Carbon::now(),
                ]);
            }
        } catch (Exception $e) {
            Log::error("[WhatsApp Service Error] Failed to send to {$recipientNumber}: " . $e->getMessage());

            $notification->update([
                'status' => 'FAILED',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $notification;
    }
}
