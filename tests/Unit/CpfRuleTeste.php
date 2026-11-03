<?php

use App\Rules\Cpf;

test('digits keeps only numbers', function () {
    expect(Cpf::digits('529.982.247-25'))->toBe('52998224725')
        ->and(Cpf::digits(null))->toBe('');
});

test('valid CPFs pass', function (string $cpf) {
    expect(Cpf::isValid($cpf))->toBeTrue();
})->with(['529.982.247-25', '52998224725', '111.444.777-35']);

test('invalid CPFs fail', function (string $cpf) {
    expect(Cpf::isValid($cpf))->toBeFalse();
})->with(['529.982.247-24', '111.111.111-11', '123.456.789-09', '000.000.000-00', '1234', '']);

test('the rule reports a message instead of passing', function () {
    $mensagens = [];
    (new Cpf)->validate('cpf', '123', function ($m) use (&$mensagens) {
        $mensagens[] = $m;
    });
    (new Cpf)->validate('cpf', '529.982.247-24', function ($m) use (&$mensagens) {
        $mensagens[] = $m;
    });

    expect($mensagens)->toHaveCount(2);
});
