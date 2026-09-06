<?php

namespace App\Services;

use App\Models\Laptop;
use App\Models\LaptopLoan;
use App\Models\QrCode;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PickupService
{
    /**
     * Process student laptop pickup scan.
     *
     * @throws Exception
     */
    public function processPickup(string $token, User $officer, ?string $laptopAssetCode = null): LaptopLoan
    {
        return DB::transaction(function () use ($token, $officer, $laptopAssetCode) {
            // Find student by QR token or NIS
            $qrCode = QrCode::where('token', $token)->where('is_active', true)->first();
            $student = $qrCode
                ? $qrCode->student
                : Student::where('nis', $token)->where('is_active', true)->first();

            if (! $student) {
                throw new Exception('Siswa dengan QR Code / NIS tersebut tidak ditemukan atau tidak aktif.');
            }

            // Check if student already has an active loan
            $existingLoan = LaptopLoan::where('student_id', $student->id)
                ->where('status', 'BORROWED')
                ->first();

            if ($existingLoan) {
                throw new Exception("Siswa {$student->name} (NIS: {$student->nis}) sudah mengambil laptop dan belum mengembalikannya.");
            }

            // Find matching active schedule if available
            $activeSchedule = Schedule::where('type', 'pickup')
                ->where('status', 'active')
                ->where(function ($q) use ($student) {
                    $q->where('class_id', $student->class_id)
                        ->orWhereNull('class_id');
                })
                ->latest()
                ->first();

            // Find laptop if asset code provided
            $laptop = null;
            if ($laptopAssetCode) {
                $laptop = Laptop::where('asset_code', $laptopAssetCode)->first();
                if (! $laptop) {
                    throw new Exception("Laptop dengan kode aset '{$laptopAssetCode}' tidak ditemukan.");
                }
                if ($laptop->status === 'borrowed') {
                    throw new Exception("Laptop '{$laptopAssetCode}' sedang dipinjam oleh siswa lain.");
                }
                $laptop->update(['status' => 'borrowed']);
            }

            // Create loan record
            $loan = LaptopLoan::create([
                'schedule_id' => $activeSchedule?->id,
                'student_id' => $student->id,
                'laptop_id' => $laptop?->id,
                'taken_by' => $officer->id,
                'taken_at' => Carbon::now(),
                'status' => 'BORROWED',
            ]);

            return $loan->load(['student.classRoom', 'student.parent', 'laptop', 'officer']);
        });
    }
}
