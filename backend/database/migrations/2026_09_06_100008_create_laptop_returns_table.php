<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptop_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('laptop_loans')->cascadeOnDelete();
            $table->foreignId('returned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('returned_at');
            $table->string('condition')->default('good'); // good, damaged, lost
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptop_returns');
    }
};
