<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class DatabaseConnectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o Laravel consegue conectar e realizar operações no SQLite em memória.
     *
     * @return void
     */
    public function test_can_connect_and_perform_db_operations_with_sqlite_in_memory()
    {
        // Verifique se a tabela de usuários existe e está vazia (graças ao RefreshDatabase)
        $this->assertDatabaseCount('users', 0);

        // Crie um novo usuário
        $user = User::factory()->create([
            'name' => 'Teste Usuário',
            'email' => 'teste@example.com',
            'password' => bcrypt('password'),
        ]);

        // Verifique se o usuário foi criado no banco de dados
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'teste@example.com',
        ]);

        // Tente buscar o usuário
        $foundUser = User::find($user->id);
        $this->assertNotNull($foundUser);
        $this->assertEquals('Teste Usuário', $foundUser->name);

        // Você pode adicionar mais operações, como atualizações e exclusões
        $foundUser->name = 'Nome Atualizado';
        $foundUser->save();

        $this->assertDatabaseHas('users', [
            'email' => 'teste@example.com',
            'name' => 'Nome Atualizado',
        ]);

        $foundUser->delete();
        $this->assertDatabaseMissing('users', [
            'email' => 'teste@example.com',
        ]);
        $this->assertDatabaseCount('users', 0);
    }

    // Você pode adicionar um teste simples para garantir que a migração funciona
    public function test_users_table_exists()
    {
        // Tenta inserir algo diretamente para confirmar que a tabela existe
        \DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }
}