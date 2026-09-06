<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_token' => ['required', 'string'],
            'condition' => ['nullable', 'string', 'in:good,damaged,lost'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
