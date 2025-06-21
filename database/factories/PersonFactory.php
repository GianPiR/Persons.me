<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Person;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['individual', 'legal_entity']);

        return [
            'name' => $type === 'individual'
                ? $this->faker->name()
                : $this->faker->company() . ' ' . $this->faker->randomElement(['Ltda', 'S.A.', 'ME', 'EIRELI']),
            'cpf' => $this->generateCpf(),
            'type' => $type,
            'phone' => $this->faker->boolean(80) ? preg_replace('/\D/', '', $this->faker->phoneNumber()) : null,
            'email' => $this->faker->boolean(70) ? $this->faker->safeEmail() : null,
        ];
    }

    /**
     * Indicate that the person is an individual.
     */
    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'individual',
            'name' => $this->faker->name(),
        ]);
    }

    /**
     * Indicate that the person is a legal entity.
     */
    public function legalEntity(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'legal_entity',
            'name' => $this->faker->company() . ' ' . $this->faker->randomElement(['Ltda', 'S.A.', 'ME', 'EIRELI']),
        ]);
    }

    /**
     * Indicate that the person has email.
     */
    public function withEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->safeEmail(),
        ]);
    }

    /**
     * Indicate that the person has phone.
     */
    public function withPhone(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => preg_replace('/\D/', '', $this->faker->phoneNumber()),
        ]);
    }

    /**
     * Generate a fake CPF for testing
     */
    private function generateCpf(): string
    {
        return str_pad((string) mt_rand(10000000000, 99999999999), 11, '0', STR_PAD_LEFT);
    }
}
