<?php

namespace App\Services;

use App\Models\LaptopLoan;
use App\Models\LaptopReturn;
use App\Models\QrCode;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    /**
     * Process student laptop return scan.
     *
     * @throws Exception
     */
    public function processReturn(string $token, User $officer, string $condition = 'good', ?string $notes = null): LaptopReturn
    {
        return DB::transaction(function () use ($token, $officer, $condition, $notes) {
            // Find student by QR token or NIS
            $qrCode = QrCode::where('token', $token)->where('is_active', true)->first();
            $student = $qrCode
                ? $qrCode->student
                : Student::where('nis', $token)->where('is_active', true)->first();

            if (! $student) {
                throw new Exception('Siswa dengan QR Code / NIS tersebut tidak ditemukan atau tidak aktif.');
            }

            // Find active loan for student
            $loan = LaptopLoan::where('student_id', $student->id)
                ->whereIn('status', ['BORROWED', 'OVERDUE'])
                ->latest()
                ->first();

            if (! $loan) {
                throw new Exception("Siswa {$student->name} (NIS: {$student->nis}) tidak memiliki transaksi pengambilan laptop yang aktif.");
            }

            // Create return record
            $returnRecord = LaptopReturn::create([
                'loan_id' => $loan->id,
                'returned_by' => $officer->id,
                'returned_at' => Carbon::now(),
                'condition' => $condition,
                'notes' => $notes,
            ]);

            // Update loan status
            $loan->update(['status' => 'RETURNED']);

            // If laptop was assigned, mark it available
            if ($loan->laptop) {
                $loan->laptop->update(['status' => 'available']);
            }

            return $returnRecord->load(['loan.student.classRoom', 'loan.student.parent', 'loan.laptop', 'officer']);
        });
    }
}
