<?php

namespace Database\Seeders;

use App\Models\User;
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
            ['Cidadão de Teste', 'cidadao@example.com', UserRole::CITIZEN],
            ['Agente de Teste', 'agente@example.com', UserRole::AGENT],
            ['Admin de Teste', 'admin@example.com', UserRole::ADMIN],
        ] as [$name, $email, $role]) {
            User::factory()->create(['name' => $name, 'email' => $email, 'role' => $role]);
        }
    }
}
