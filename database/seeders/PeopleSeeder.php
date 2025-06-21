<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use Faker\Factory as Faker;

class PeopleSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        // Limpar dados existentes
        Person::truncate();

        // Criar algumas pessoas fixas para testes
        $fixedPeople = [
            [
                'name' => 'João Silva Santos',
                'cpf' => '12345678901',
                'type' => 'individual',
                'phone' => '11999999999',
                'email' => 'joao.silva@email.com',
            ],
            [
                'name' => 'Maria Oliveira Costa',
                'cpf' => '98765432100',
                'type' => 'individual',
                'phone' => '11888888888',
                'email' => 'maria.oliveira@email.com',
            ],
            [
                'name' => 'Empresa ABC Ltda',
                'cpf' => '11222333000',
                'type' => 'legal_entity',
                'phone' => '1133334444',
                'email' => 'contato@empresaabc.com.br',
            ],
            [
                'name' => 'Tech Solutions S.A.',
                'cpf' => '55666777000',
                'type' => 'legal_entity',
                'phone' => '1155556666',
                'email' => 'info@techsolutions.com.br',
            ],
        ];

        foreach ($fixedPeople as $person) {
            Person::create($person);
        }

        // Criar pessoas aleatórias usando Faker
        for ($i = 0; $i < 20; $i++) {
            $type = $faker->randomElement(['individual', 'legal_entity']);

            Person::create([
                'name' => $type === 'individual'
                    ? $faker->name
                    : $faker->company . ' ' . $faker->randomElement(['Ltda', 'S.A.', 'ME', 'EIRELI']),
                'cpf' => $this->generateCpf(),
                'type' => $type,
                'phone' => $faker->boolean(80) ? preg_replace('/\D/', '', $faker->cellphoneNumber()) : null,
                'email' => $faker->boolean(70) ? $faker->email : null,
            ]);
        }
    }

    /**
     * Gerar um CPF/CNPJ válido para teste
     */
    private function generateCpf(): string
    {
        return str_pad(mt_rand(10000000000, 99999999999), 11, '0', STR_PAD_LEFT);
    }
}
