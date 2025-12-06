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
        // Criar ou atualizar usuário padrão para acesso ao sistema
        User::updateOrCreate(
            ['email' => 'admin@cevesp.com.br'],
            [
                'name' => 'Administrador',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'administrador',
            ]
        );
    }
}
