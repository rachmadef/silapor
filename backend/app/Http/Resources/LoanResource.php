<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'taken_at' => $this->taken_at?->toIso8601String(),
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'nis' => $this->student->nis,
                'name' => $this->student->name,
                'class_name' => $this->student->classRoom?->name,
                'parent_name' => $this->student->parent?->name,
                'parent_whatsapp' => $this->student->parent?->whatsapp_number,
            ]),
            'laptop' => $this->whenLoaded('laptop', fn () => [
                'id' => $this->laptop->id,
                'asset_code' => $this->laptop->asset_code,
                'serial_number' => $this->laptop->serial_number,
            ]),
            'officer' => $this->whenLoaded('officer', fn () => [
                'id' => $this->officer->id,
                'name' => $this->officer->name,
            ]),
            'return_record' => $this->whenLoaded('returnRecord', fn () => [
                'id' => $this->returnRecord->id,
                'returned_at' => $this->returnRecord->returned_at?->toIso8601String(),
                'condition' => $this->returnRecord->condition,
                'notes' => $this->returnRecord->notes,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
