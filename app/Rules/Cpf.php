<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    /**
     * Mantém só os dígitos do CPF (aceita "529.982.247-25" ou "52998224725").
     */
    public static function digits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    /**
     * Confere tamanho, sequências repetidas e os dois dígitos verificadores.
     */
    public static function isValid(?string $value): bool
    {
        $cpf = self::digits($value);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        if (in_array(substr($cpf, 0, 9), ['123456789', '987654321'], true)) {
            return false;
        }

        foreach ([9, 10] as $length) {
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $cpf[$i] * ($length + 1 - $i);
            }

            $digit = ($sum * 10) % 11;
            $digit = $digit === 10 ? 0 : $digit;

            if ((int) $cpf[$length] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = self::digits(is_scalar($value) ? (string) $value : '');

        if (strlen($digits) !== 11) {
            $fail('O CPF deve ter exatamente 11 dígitos.');

            return;
        }

        if (! self::isValid($digits)) {
            $fail('O CPF informado é inválido.');
        }
    }
}
