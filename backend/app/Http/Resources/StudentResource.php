<?php

namespace App\Http\Resources;

use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $qrCodeService = app(QrCodeService::class);

        return [
            'id' => $this->id,
            'nis' => $this->nis,
            'name' => $this->name,
            'class' => $this->whenLoaded('classRoom', fn () => $this->classRoom ? [
                'id' => $this->classRoom->id,
                'name' => $this->classRoom->name,
                'grade' => $this->classRoom->grade,
            ] : null),
            'parent' => $this->whenLoaded('parent', fn () => $this->parent ? [
                'id' => $this->parent->id,
                'name' => $this->parent->name,
                'whatsapp_number' => $this->parent->whatsapp_number,
            ] : null),
            'qr_code' => $this->whenLoaded('qrCode', function () use ($qrCodeService) {
                if (! $this->qrCode) {
                    $qr = $qrCodeService->generateForStudent($this->resource);
                    return [
                        'id' => $qr->id,
                        'token' => $qr->token,
                        'qr_image_url' => $qrCodeService->getQrCodeDataUri($qr->token),
                    ];
                }
                return [
                    'id' => $this->qrCode->id,
                    'token' => $this->qrCode->token,
                    'qr_image_url' => $qrCodeService->getQrCodeDataUri($this->qrCode->token),
                ];
            }),
            'active_loan' => $this->whenLoaded('activeLoan', fn () => $this->activeLoan ? new LoanResource($this->activeLoan) : null),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
