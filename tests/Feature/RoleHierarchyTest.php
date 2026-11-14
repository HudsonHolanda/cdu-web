<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:agent'])->get('/_t/agent', fn () => 'ok');
    Route::middleware(['web', 'auth', 'role:admin'])->get('/_t/admin', fn () => 'ok');
});

it('permite admin acessar rota de agent (hierarquia cumulativa)', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/_t/agent')->assertOk();
});

it('bloqueia citizen em rota de agent com 403', function () {
    $this->actingAs(User::factory()->create())
        ->get('/_t/agent')->assertForbidden();
});

it('bloqueia agent em rota de admin com 403', function () {
    $this->actingAs(User::factory()->agent()->create())
        ->get('/_t/admin')->assertForbidden();
});

it('aplica os gates de forma cumulativa', function () {
    $agent = User::factory()->agent()->create();

    expect($agent->can('report-problems'))->toBeTrue()
        ->and($agent->can('manage-resolutions'))->toBeTrue()
        ->and($agent->can('manage-agents'))->toBeFalse();
});