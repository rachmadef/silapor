<?php

namespace App\Jobs;

use App\Models\LaptopLoan;
use App\Models\ParentModel;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public Student $student,
        public ParentModel $parent,
        public LaptopLoan $loan
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        $whatsAppService->sendOverdueNotification(
            $this->student,
            $this->parent,
            $this->loan
        );
    }
}
