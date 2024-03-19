<?php

namespace Tests\Feature\Auth;

use App\Filament\Pages\Auth\Register;
use function Pest\Livewire\livewire;

test('registration screen can be rendered', function () {
    $response = $this->get('app/register');

    $response
        ->assertOk();
});

test('new users can register', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0712345678',
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});
