<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laptops', function (Blueprint $table) {
            if (! Schema::hasColumn('laptops', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('id')->constrained('students')->nullOnDelete();
            }
            if (! Schema::hasColumn('laptops', 'brand')) {
                $table->string('brand')->nullable()->after('asset_code');
            }
            if (! Schema::hasColumn('laptops', 'model')) {
                $table->string('model')->nullable()->after('brand');
            }
        });

        // Kosongkan data dummy laptop bawaan seeder
        DB::table('laptop_loans')->whereNotNull('laptop_id')->update(['laptop_id' => null]);
        DB::table('laptops')->where('asset_code', 'like', 'LAP-0%')->whereNull('student_id')->delete();
    }

    public function down(): void
    {
        Schema::table('laptops', function (Blueprint $table) {
            if (Schema::hasColumn('laptops', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('laptops', 'brand')) {
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('laptops', 'model')) {
                $table->dropColumn('model');
            }
        });
    }
};

