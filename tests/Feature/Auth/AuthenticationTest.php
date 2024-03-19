<?php

use App\Models\User;
use Livewire\Volt\Volt;
use function Pest\Livewire\livewire;

test('login screen can be rendered', function () {
    $response = $this->get('app/login');

    $response
        ->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    livewire(\Filament\Pages\Auth\Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();
    livewire(\Filament\Pages\Auth\Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasErrors();

    $this->assertGuest();
});
