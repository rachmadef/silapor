<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\LaptopLoan;
use Illuminate\Console\Command;

class CheckOverdueLaptops extends Command
{
    protected $signature = 'laptops:check-overdue';

    protected $description = 'Cari transaksi pengambilan laptop yang belum dikembalikan setelah deadline (15.00) dan kirim notifikasi WA ke wali murid.';

    public function handle(): int
    {
        $this->info('Memulai pemeriksaan transaksi laptop yang mengalami keterlambatan...');

        $overdueLoans = LaptopLoan::with(['student.parent', 'student.classRoom'])
            ->where('status', 'BORROWED')
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info('Tidak ditemukan transaksi laptop yang belum dikembalikan.');

            return Command::SUCCESS;
        }

        $processedCount = 0;
        $notifiedCount = 0;

        foreach ($overdueLoans as $loan) {
            // Update loan status to OVERDUE
            $loan->update(['status' => 'OVERDUE']);
            $processedCount++;

            $student = $loan->student;
            $parent = $student?->parent;

            if ($student && $parent && $parent->whatsapp_number) {
                // Dispatch notification job
                SendWhatsAppNotification::dispatch($student, $parent, $loan);
                $notifiedCount++;
            }
        }

        $this->info("Pemeriksaan selesai. {$processedCount} transaksi diubah statusnya menjadi OVERDUE. {$notifiedCount} job notifikasi WhatsApp telah di-queue.");

        return Command::SUCCESS;
    }
}
