<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_token' => ['required', 'string'],
            'laptop_asset_code' => ['nullable', 'string'],
        ];
    }
}
