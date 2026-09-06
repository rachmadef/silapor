<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laptop extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'serial_number',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LaptopLoan::class, 'laptop_id');
    }
}
