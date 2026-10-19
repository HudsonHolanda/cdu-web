
<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register with a valid CPF', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('cpf', '529.982.247-25')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'cpf' => '52998224725',
    ]);

    $user = User::where('email', 'test@example.com')->firstOrFail();

    expect(Hash::check('password', $user->password))->toBeTrue();
});

test('invalid CPF check digits are rejected', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('cpf', '529.982.247-24')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['cpf'])
        ->assertSet('cpf', '529.982.247-24');

    $this->assertGuest();

    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com',
    ]);
});

test('repeated CPF digits are rejected', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('cpf', '111.111.111-11')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['cpf']);

    $this->assertGuest();
});

test('ascending and descending CPF sequences are rejected', function (
    string $cpf
) {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('cpf', $cpf)
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['cpf'])
        ->assertSet('cpf', $cpf);

    $this->assertGuest();
})->with([
    'ascending sequence' => '123.456.789-09',
    'descending sequence' => '987.654.321-00',
]);

test('CPF with fewer than eleven digits is rejected', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('cpf', '123.456.789')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['cpf'])
        ->assertSet('cpf', '123.456.789');

    $this->assertGuest();
});

test('duplicate CPF registrations are rejected', function () {
    User::factory()->create([
        'cpf' => '52998224725',
    ]);

    $component = Volt::test('pages.auth.register')
        ->set('name', 'Another User')
        ->set('email', 'another@example.com')
        ->set('cpf', '529.982.247-25')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register')
        ->assertHasErrors(['cpf'])
        ->assertSet('cpf', '529.982.247-25');

    $this->assertGuest();

    $this->assertDatabaseMissing('users', [
        'email' => 'another@example.com',
    ]);
});
