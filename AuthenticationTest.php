<?php

use App\Models\User;

/**
 * Added this tests
 */
test('throttle of 1 min works correctly', function () {
    $user = User::factory()->create()->refresh();

    $this->freezeTime();
    for ($i = 0; $i <= 5; ++$i) {
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    };

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'email' => 'Too many login attempts. Please try again in 60 seconds.'
    ]);
});
/**
 * Ends here
 */

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
