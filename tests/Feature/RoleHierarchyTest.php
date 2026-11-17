<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'can:access-agent-panel'])->get('/_t/agent', fn () => 'ok');
    Route::middleware(['web', 'auth', 'can:manage-agents'])->get('/_t/admin', fn () => 'ok');
});

it('usa rotas do painel do agente sem prefixo agent', function () {
    expect(route('agent.overview', [], false))->toBe('/painel')
        ->and(route('agent.dashboard', [], false))->toBe('/painel/dashboard')
        ->and(route('agent.mapa', [], false))->toBe('/painel/mapa')
        ->and(route('agent.relatorios', [], false))->toBe('/painel/relatorios');
});

it('permite agent acessar rota do painel do agente', function () {
    $this->actingAs(User::factory()->agent()->create())
        ->get('/_t/agent')->assertOk();
});

it('bloqueia admin em rota de agent com 403', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/_t/agent')->assertForbidden();
});

it('bloqueia citizen em rota de agent com 403', function () {
    $this->actingAs(User::factory()->create())
        ->get('/_t/agent')->assertForbidden();
});

it('bloqueia agent em rota de admin com 403', function () {
    $this->actingAs(User::factory()->agent()->create())
        ->get('/_t/admin')->assertForbidden();
});

it('aplica os gates do painel do agente de forma estrita', function () {
    $agent = User::factory()->agent()->create();
    $admin = User::factory()->admin()->create();

    expect($agent->can('report-problems'))->toBeTrue()
        ->and($agent->can('access-agent-panel'))->toBeTrue()
        ->and($agent->can('manage-agents'))->toBeFalse()
        ->and($admin->can('access-agent-panel'))->toBeFalse();
});