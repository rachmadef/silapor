<?php

use App\Models\User;

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'role' => 'officer',
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
});

test('inactive user cannot login', function () {
    $user = User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password'),
        'is_active' => false,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'inactive@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(403);
});
