<?php

// Só as regras usadas hoje; o que faltar cai no inglês do framework (fallback_locale).
return [
    'confirmed' => 'A confirmação de :attribute não confere.',
    'current_password' => 'A senha atual está incorreta.',
    'email' => ':Attribute precisa ser um e-mail válido.',
    'lowercase' => ':Attribute deve estar em letras minúsculas.',
    'max' => [
        'string' => ':Attribute não pode ter mais de :max caracteres.',
    ],
    'min' => [
        'string' => ':Attribute precisa ter pelo menos :min caracteres.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => ':Attribute deve ser um texto.',
    'unique' => ':Attribute já está em uso.',
    'password' => [
        'letters' => ':Attribute precisa ter pelo menos uma letra.',
        'mixed' => ':Attribute precisa ter pelo menos uma letra maiúscula e uma minúscula.',
        'numbers' => ':Attribute precisa ter pelo menos um número.',
        'symbols' => ':Attribute precisa ter pelo menos um símbolo.',
        'uncompromised' => ':Attribute apareceu em um vazamento de dados. Escolha outra.',
    ],
    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'cpf' => 'CPF',
        'password' => 'senha',
        'password_confirmation' => 'confirmação da senha',
        'current_password' => 'senha atual',
    ],
];
