<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'returned_at' => $this->returned_at?->toIso8601String(),
            'condition' => $this->condition,
            'notes' => $this->notes,
            'loan' => $this->whenLoaded('loan', fn () => new LoanResource($this->loan)),
            'officer' => $this->whenLoaded('officer', fn () => [
                'id' => $this->officer->id,
                'name' => $this->officer->name,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
