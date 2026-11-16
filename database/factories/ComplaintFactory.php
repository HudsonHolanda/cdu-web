<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'title' => fake()->randomElement([
                'Buraco na rua', 'Iluminação queimada', 'Lixo acumulado',
                'Vazamento de água', 'Semáforo com defeito', 'Árvore caída',
            ]),
            'description' => fake()->paragraph(),
            'image_url' => null,
            'status' => ComplaintStatus::PENDING,
            'high_priority' => false,
            // Coordenadas dentro de São Paulo
            'latitude' => fake()->latitude(-23.70, -23.45),
            'longitude' => fake()->longitude(-46.75, -46.45),
            'address' => fake('pt_BR')->streetAddress(),
            'district' => fake()->randomElement(['Centro', 'Mooca', 'Pinheiros', 'Tatuapé', 'Santana']),
            'cep' => fake()->numerify('########'),
            'complement' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::IN_PROGRESS]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::RESOLVED]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => ComplaintStatus::REJECTED]);
    }

    public function highPriority(): static
    {
        return $this->state(fn () => ['high_priority' => true]);
    }
}