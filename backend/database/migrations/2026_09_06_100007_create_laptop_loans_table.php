<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptop_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('laptop_id')->nullable()->constrained('laptops')->nullOnDelete();
            $table->foreignId('taken_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('taken_at');
            $table->string('status')->default('BORROWED'); // BORROWED, RETURNED, OVERDUE
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptop_loans');
    }
};
