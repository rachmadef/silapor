<?php

use App\Models\ClassRoom;
use App\Models\Laptop;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use App\Services\QrCodeService;
use Laravel\Sanctum\Sanctum;

test('student can pickup laptop via scan', function () {
    $officer = User::factory()->create(['role' => 'officer']);
    Sanctum::actingAs($officer);

    $class = ClassRoom::create(['name' => 'X RPL 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
    $parent = ParentModel::create(['name' => 'Wali Test', 'whatsapp_number' => '628123456789']);
    $student = Student::create(['nis' => '1001', 'name' => 'Siswa Test', 'class_id' => $class->id, 'parent_id' => $parent->id]);

    $qrCodeService = app(QrCodeService::class);
    $qrCode = $qrCodeService->generateForStudent($student);

    $laptop = Laptop::create(['asset_code' => 'LAP-001', 'status' => 'available']);

    // Process pickup
    $response = $this->postJson('/api/pickups/scan', [
        'qr_token' => $qrCode->token,
        'laptop_asset_code' => 'LAP-001',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.status', 'BORROWED');

    $this->assertDatabaseHas('laptop_loans', [
        'student_id' => $student->id,
        'laptop_id' => $laptop->id,
        'status' => 'BORROWED',
    ]);

    $this->assertDatabaseHas('laptops', [
        'id' => $laptop->id,
        'status' => 'borrowed',
    ]);
});

test('duplicate pickup scan returns error', function () {
    $officer = User::factory()->create(['role' => 'officer']);
    Sanctum::actingAs($officer);

    $class = ClassRoom::create(['name' => 'X RPL 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
    $student = Student::create(['nis' => '1002', 'name' => 'Siswa Test 2', 'class_id' => $class->id]);
    $qrCode = app(QrCodeService::class)->generateForStudent($student);

    // First pickup
    $this->postJson('/api/pickups/scan', ['qr_token' => $qrCode->token]);

    // Second pickup (duplicate)
    $response = $this->postJson('/api/pickups/scan', ['qr_token' => $qrCode->token]);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error');
});

test('student can return laptop via scan', function () {
    $officer = User::factory()->create(['role' => 'officer']);
    Sanctum::actingAs($officer);

    $class = ClassRoom::create(['name' => 'X RPL 1', 'grade' => 'X', 'academic_year' => '2025/2026']);
    $student = Student::create(['nis' => '1003', 'name' => 'Siswa Test 3', 'class_id' => $class->id]);
    $qrCode = app(QrCodeService::class)->generateForStudent($student);
    $laptop = Laptop::create(['asset_code' => 'LAP-002', 'status' => 'available']);

    // Pickup
    $this->postJson('/api/pickups/scan', [
        'qr_token' => $qrCode->token,
        'laptop_asset_code' => 'LAP-002',
    ]);

    // Return
    $response = $this->postJson('/api/returns/scan', [
        'qr_token' => $qrCode->token,
        'condition' => 'good',
        'notes' => 'Dikembalikan rapi',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseHas('laptop_loans', [
        'student_id' => $student->id,
        'status' => 'RETURNED',
    ]);

    $this->assertDatabaseHas('laptops', [
        'id' => $laptop->id,
        'status' => 'available',
    ]);
});
