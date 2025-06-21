<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Executar seeders específicos
        $this->call([
            UserSeeder::class,
            PeopleSeeder::class,
        ]);
        
        $this->command->info('✅ Dados de teste criados com sucesso!');
        $this->command->info('🔐 Credenciais de login:');
        $this->command->info('   📧 Email: teste@exemplo.com');
        $this->command->info('   🔑 Senha: 123456');
        $this->command->info('');
        $this->command->info('👨‍💼 Outros usuários disponíveis:');
        $this->command->info('   📧 admin@sistema.com (senha: 123456)');
        $this->command->info('   📧 maria@exemplo.com (senha: 123456)');
        $this->command->info('   📧 joao@exemplo.com (senha: 123456)');
    }
}