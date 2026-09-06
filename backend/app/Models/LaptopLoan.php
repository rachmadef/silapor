<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaptopLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'student_id',
        'laptop_id',
        'taken_by',
        'taken_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function laptop(): BelongsTo
    {
        return $this->belongsTo(Laptop::class, 'laptop_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function returnRecord(): HasOne
    {
        return $this->hasOne(LaptopReturn::class, 'loan_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'loan_id');
    }
}
