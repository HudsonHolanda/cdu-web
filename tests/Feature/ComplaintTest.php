<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\User;

test('cria o relato com os valores padrão e os casts corretos', function () {
    $complaint = Complaint::factory()->create()->fresh();

    expect($complaint->status)->toBe(ComplaintStatus::PENDING)
        ->and($complaint->high_priority)->toBeFalse()
        ->and($complaint->latitude)->toBeFloat();
});

test('o relato pertence ao seu autor', function () {
    $user = User::factory()->create();
    $complaint = Complaint::factory()->create(['id_user' => $user->id_user]);

    expect($complaint->user->is($user))->toBeTrue();
});

test('os estados da factory definem o status e a prioridade esperados', function () {
    expect(Complaint::factory()->inProgress()->create()->status)->toBe(ComplaintStatus::IN_PROGRESS)
        ->and(Complaint::factory()->resolved()->create()->status)->toBe(ComplaintStatus::RESOLVED)
        ->and(Complaint::factory()->rejected()->create()->status)->toBe(ComplaintStatus::REJECTED)
        ->and(Complaint::factory()->highPriority()->create()->high_priority)->toBeTrue();
});

test('status, prioridade e autor não podem ser preenchidos em massa', function () {
    $complaint = new Complaint([
        'title' => 'Buraco na rua',
        'status' => ComplaintStatus::RESOLVED->value,
        'high_priority' => true,
        'id_user' => User::factory()->create()->id_user,
    ]);

    expect($complaint->title)->toBe('Buraco na rua')
        ->and($complaint->status)->toBeNull()
        ->and($complaint->high_priority)->toBeNull()
        ->and($complaint->id_user)->toBeNull();
});

test('o status pode ser alterado explicitamente', function () {
    $complaint = Complaint::factory()->create();

    $complaint->status = ComplaintStatus::REJECTED;
    $complaint->save();

    expect($complaint->fresh()->status)->toBe(ComplaintStatus::REJECTED)
        ->and(ComplaintStatus::REJECTED->label())->toBe('Rejeitado');
});