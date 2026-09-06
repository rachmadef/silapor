<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaptopReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'returned_by',
        'returned_at',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'returned_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(LaptopLoan::class, 'loan_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}
