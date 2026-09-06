<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Laptop;
use App\Models\ParentModel;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $qrCodeService = app(QrCodeService::class);

        // 1. Create Default Users
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@silapor.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $officer = User::create([
            'name' => 'Petugas Lab',
            'email' => 'petugas@silapor.com',
            'password' => Hash::make('password'),
            'role' => 'officer',
            'is_active' => true,
        ]);

        // 2. Create Classes
        $class1 = ClassRoom::create(['name' => 'X RPL 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
        $class2 = ClassRoom::create(['name' => 'XI TKJ 1', 'grade' => 'XI', 'academic_year' => '2025/2026']);
        $class3 = ClassRoom::create(['name' => 'XII DKV 1', 'grade' => 'XII', 'academic_year' => '2025/2026']);

        // 3. Create Parents
        $parent1 = ParentModel::create(['name' => 'Budi Santoso', 'whatsapp_number' => '6281234567890']);
        $parent2 = ParentModel::create(['name' => 'Siti Rahma', 'whatsapp_number' => '6281987654321']);
        $parent3 = ParentModel::create(['name' => 'Agus Setiawan', 'whatsapp_number' => '6285678901234']);

        // 4. Create Students
        $studentsData = [
            ['nis' => '2026001', 'name' => 'Ahmad Rizky', 'class_id' => $class1->id, 'parent_id' => $parent1->id],
            ['nis' => '2026002', 'name' => 'Dewi Lestari', 'class_id' => $class1->id, 'parent_id' => $parent2->id],
            ['nis' => '2026003', 'name' => 'Bagas Pratama', 'class_id' => $class2->id, 'parent_id' => $parent3->id],
            ['nis' => '2026004', 'name' => 'Citra Kirana', 'class_id' => $class2->id, 'parent_id' => $parent1->id],
            ['nis' => '2026005', 'name' => 'Eko Prasetyo', 'class_id' => $class3->id, 'parent_id' => $parent2->id],
        ];

        foreach ($studentsData as $data) {
            $student = Student::create($data);
            $qrCodeService->generateForStudent($student);
        }

        // 5. Create Laptops
        for ($i = 1; $i <= 10; $i++) {
            Laptop::create([
                'asset_code' => sprintf('LAP-%03d', $i),
                'serial_number' => 'SN-LENOVO-' . (1000 + $i),
                'status' => 'available',
                'is_active' => true,
            ]);
        }

        // 6. Create Daily Schedules
        Schedule::create([
            'class_id' => null, // All classes
            'type' => 'pickup',
            'start_at' => Carbon::today()->setHour(7)->setMinute(30),
            'end_at' => Carbon::today()->setHour(9)->setMinute(0),
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        Schedule::create([
            'class_id' => null,
            'type' => 'return',
            'start_at' => Carbon::today()->setHour(13)->setMinute(0),
            'end_at' => Carbon::today()->setHour(15)->setMinute(0),
            'status' => 'active',
            'created_by' => $admin->id,
        ]);
    }
}
