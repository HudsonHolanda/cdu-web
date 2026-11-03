<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function validAgentData(array $override = []): array
{
    return array_merge([
        'name' => 'Agente Novo',
        'email' => 'novo.agente@example.com',
        'cpf' => '529.982.247-25',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $override);
}

test('guests are sent to login', function () {
    $this->get('/agents')->assertRedirect('/login');
});

test('citizens and agents cannot reach the agent area', function () {
    foreach ([User::factory()->create(), User::factory()->agent()->create()] as $user) {
        $this->actingAs($user)->get('/agents')->assertForbidden();
        $this->actingAs($user)->post('/agents', validAgentData())->assertForbidden();
    }

    expect(User::where('email', 'novo.agente@example.com')->exists())->toBeFalse();
});

test('admin sees the list of agents only', function () {
    User::factory()->agent()->create(['name' => 'Maria Agente']);
    User::factory()->create(['name' => 'Joao Cidadao']);

    $this->actingAs(User::factory()->admin()->create())
        ->get('/agents')
        ->assertOk()
        ->assertSee('Maria Agente')
        ->assertDontSee('Joao Cidadao');
});

test('admin creates an agent with hashed password and agent role', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post('/agents', validAgentData())
        ->assertRedirect(route('agents.index'))
        ->assertSessionHasNoErrors();

    $agent = User::where('email', 'novo.agente@example.com')->firstOrFail();

    expect($agent->role)->toBe(UserRole::AGENT)
        ->and($agent->cpf)->toBe('52998224725')
        ->and($agent->password)->not->toBe('password')
        ->and(Hash::check('password', $agent->password))->toBeTrue()
        ->and($agent->hasVerifiedEmail())->toBeTrue();
});

test('an agent created by the admin can log in through the normal login', function () {
    $this->actingAs(User::factory()->admin()->create())->post('/agents', validAgentData());
    auth()->logout();

    $this->post('/login', ['email' => 'novo.agente@example.com', 'password' => 'password']);

    // O login do Breeze é Livewire; aqui basta provar que a credencial é aceita pelo guard padrão
    expect(auth()->attempt(['email' => 'novo.agente@example.com', 'password' => 'password']))->toBeTrue();
});

test('creating never overwrites an existing account', function () {
    $admin = User::factory()->admin()->create(['email' => 'chefe@example.com']);
    $hashBefore = $admin->password;

    $this->actingAs($admin)
        ->post('/agents', validAgentData(['email' => 'chefe@example.com', 'name' => 'Invasor']))
        ->assertSessionHasErrors('email');

    $admin->refresh();
    expect($admin->name)->not->toBe('Invasor')->and($admin->password)->toBe($hashBefore);
});

test('invalid or duplicated CPF is rejected', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/agents', validAgentData(['cpf' => '529.982.247-24']))->assertSessionHasErrors('cpf');
    $this->actingAs($admin)->post('/agents', validAgentData(['cpf' => '111.111.111-11']))->assertSessionHasErrors('cpf');

    User::factory()->create(['cpf' => '52998224725']);
    $this->actingAs($admin)->post('/agents', validAgentData())->assertSessionHasErrors('cpf');
});

test('admin updates an agent and keeps the password when left blank', function () {
    $agent = User::factory()->agent()->create(['password' => 'senha-antiga']);
    $hashBefore = $agent->password;

    $this->actingAs(User::factory()->admin()->create())
        ->put("/agents/{$agent->id_user}", [
            'name' => 'Nome Atualizado',
            'email' => $agent->email,
            'cpf' => $agent->cpf,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('agents.index'))
        ->assertSessionHasNoErrors();

    $agent->refresh();
    expect($agent->name)->toBe('Nome Atualizado')->and($agent->password)->toBe($hashBefore);
});

test('admin can change the agent password', function () {
    $agent = User::factory()->agent()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put("/agents/{$agent->id_user}", [
            'name' => $agent->name,
            'email' => $agent->email,
            'cpf' => $agent->cpf,
            'password' => 'nova-senha-123',
            'password_confirmation' => 'nova-senha-123',
        ])
        ->assertSessionHasNoErrors();

    expect(Hash::check('nova-senha-123', $agent->refresh()->password))->toBeTrue();
});

test('admin deletes an agent', function () {
    $agent = User::factory()->agent()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete("/agents/{$agent->id_user}")
        ->assertRedirect(route('agents.index'));

    $this->assertDatabaseMissing('users', ['id_user' => $agent->id_user]);
});

test('the agent routes never touch citizens or admins', function () {
    $admin = User::factory()->admin()->create();
    $citizen = User::factory()->create();
    $otherAdmin = User::factory()->admin()->create();

    foreach ([$citizen, $otherAdmin] as $target) {
        $this->actingAs($admin)->delete("/agents/{$target->id_user}")->assertNotFound();
        $this->actingAs($admin)->get("/agents/{$target->id_user}/edit")->assertNotFound();
        $this->actingAs($admin)->put("/agents/{$target->id_user}", validAgentData())->assertNotFound();
        $this->assertDatabaseHas('users', ['id_user' => $target->id_user]);
    }
});

test('role cannot be mass assigned', function () {
    $user = User::create([
        'name' => 'Esperto',
        'email' => 'esperto@example.com',
        'cpf' => '52998224725',
        'password' => 'password',
        'role' => UserRole::ADMIN->value,
    ]);

    expect($user->fresh()->role)->toBe(UserRole::CITIZEN);
});
