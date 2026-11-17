<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $citizen = User::where('email', 'cidadao@example.com')->firstOrFail();

        // Um relato de cada status + um prioritário, todos do cidadão de teste
        foreach ([
            Complaint::factory(),
            Complaint::factory()->inProgress(),
            Complaint::factory()->resolved(),
            Complaint::factory()->rejected(),
            Complaint::factory()->highPriority(),
        ] as $factory) {
            $factory->create(['id_user' => $citizen->id_user]);
        }
    }
}