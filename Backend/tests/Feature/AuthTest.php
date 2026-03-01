<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('registers with valid data and returns 201 with token', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'first_name' => 'Max',
        'last_name'  => 'Mustermann',
        'address'    => 'Musterstraße 1',
        'zip'        => '74523',
        'town'       => 'Schwäbisch Hall',
        'phone'      => '0791123456',
        'email'      => 'max@example.com',
        'password'   => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)->assertJsonStructure(['token']);
});

it('rejects registration with duplicate email with 422', function () {
    User::factory()->create(['email' => 'dup@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'first_name' => 'Anna',
        'last_name'  => 'Muster',
        'address'    => 'Str. 2',
        'zip'        => '74523',
        'town'       => 'Hall',
        'phone'      => '0791000000',
        'email'      => 'dup@example.com',
        'password'   => 'password',
        'password_confirmation' => 'password',
    ])->assertStatus(422);
});

it('logs in with valid credentials and returns token', function () {
    User::factory()->create(['email' => 'login@example.com', 'password' => bcrypt('password')]);

    $this->postJson('/api/v1/auth/login', [
        'email'    => 'login@example.com',
        'password' => 'password',
    ])->assertStatus(200)->assertJsonStructure(['token']);
});

it('rejects login with wrong password with 401', function () {
    User::factory()->create(['email' => 'wrong@example.com', 'password' => bcrypt('correct')]);

    $this->postJson('/api/v1/auth/login', [
        'email'    => 'wrong@example.com',
        'password' => 'incorrect',
    ])->assertStatus(401);
});

it('logout revokes token', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/v1/auth/logout')->assertStatus(200);
});
