<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Limpar dados existentes
        User::truncate();
        
        // Criar usuários de teste
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'name' => 'João Santos',
            'email' => 'joao@exemplo.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
