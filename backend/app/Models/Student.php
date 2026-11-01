<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'class_id',
        'parent_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function qrCode(): HasOne
    {
        return $this->hasOne(QrCode::class, 'student_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LaptopLoan::class, 'student_id');
    }

    public function laptop(): HasOne
    {
        return $this->hasOne(Laptop::class, 'student_id');
    }

    public function activeLoan(): HasOne
    {
        return $this->hasOne(LaptopLoan::class, 'student_id')->where('status', 'BORROWED')->latestOfMany();
    }
}
