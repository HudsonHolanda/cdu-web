<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        foreach ([
            ['Cidadão de Teste', 'cidadao@example.com', '52998224725', UserRole::CITIZEN],
            ['Agente de Teste', 'agente@example.com', '11144477735', UserRole::AGENT],
            ['Admin de Teste', 'admin@example.com', '93541134780', UserRole::ADMIN],
        ] as [$name, $email, $cpf, $role]) {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'cpf' => $cpf,
                    'role' => $role,
                    'password' => 'password',
                ],
            );
        }

        $this->call(ComplaintSeeder::class);
    }
}
