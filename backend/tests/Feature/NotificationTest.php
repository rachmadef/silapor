<?php

use App\Jobs\SendWhatsAppNotification;
use App\Models\ClassRoom;
use App\Models\Laptop;
use App\Models\LaptopLoan;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

test('laptops check overdue command updates loan status and dispatches notification job', function () {
    Queue::fake();

    $officer = User::factory()->create(['role' => 'officer']);
    $class = ClassRoom::create(['name' => 'X RPL 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
    $parent = ParentModel::create(['name' => 'Wali Overdue', 'whatsapp_number' => '6281234567890']);
    $student = Student::create(['nis' => '9001', 'name' => 'Siswa Overdue', 'class_id' => $class->id, 'parent_id' => $parent->id]);

    $loan = LaptopLoan::create([
        'student_id' => $student->id,
        'taken_by' => $officer->id,
        'taken_at' => Carbon::now()->subHours(8),
        'status' => 'BORROWED',
    ]);

    // Run command
    Artisan::call('laptops:check-overdue');

    // Assert status updated to OVERDUE
    $this->assertDatabaseHas('laptop_loans', [
        'id' => $loan->id,
        'status' => 'OVERDUE',
    ]);

    // Assert job dispatched
    Queue::assertPushed(SendWhatsAppNotification::class, function ($job) use ($student) {
        return $job->student->id === $student->id;
    });
});

test('authenticated officer can trigger overdue check via API', function () {
    Queue::fake();

    $officer = User::factory()->create(['role' => 'officer']);
    Sanctum::actingAs($officer);

    $response = $this->postJson('/api/notifications/check-overdue');

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Pemeriksaan transaksi overdue berhasil dijalankan.');
});
