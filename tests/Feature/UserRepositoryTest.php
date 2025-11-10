<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    // ====================================
    // TESTES QUE SALVAM NO BANCO DE DADOS
    // ====================================

    public function test_deve_criar_usuario_no_banco_de_dados()
    {
        // Arrange
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => bcrypt('senha123')
        ];

        // Act
        $user = $this->repository->create($userData);

        // Assert
        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@example.com'
        ]);

        $this->assertEquals('João Silva', $user->name);
        $this->assertEquals('joao@example.com', $user->email);
        $this->assertNotNull($user->id);
    }

    public function test_deve_buscar_usuario_por_id_no_banco()
    {
        // Arrange - cria um usuário no banco
        $createdUser = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria@example.com'
        ]);

        // Act - busca o usuário pelo ID
        $foundUser = $this->repository->find($createdUser->id);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals('Maria Santos', $foundUser->name);
        $this->assertEquals('maria@example.com', $foundUser->email);
        $this->assertEquals($createdUser->id, $foundUser->id);
    }

    public function test_deve_atualizar_usuario_no_banco_de_dados()
    {
        // Arrange - cria um usuário
        $user = User::factory()->create([
            'name' => 'Pedro Oliveira',
            'email' => 'pedro@example.com'
        ]);

        // Act - atualiza o nome
        $updatedUser = $this->repository->update($user->id, [
            'name' => 'Pedro Oliveira Atualizado'
        ]);

        // Assert
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Pedro Oliveira Atualizado',
            'email' => 'pedro@example.com'
        ]);

        $this->assertEquals('Pedro Oliveira Atualizado', $updatedUser->name);
    }

    public function test_deve_deletar_usuario_do_banco_de_dados()
    {
        // Arrange - cria um usuário
        $user = User::factory()->create([
            'name' => 'Ana Costa',
            'email' => 'ana@example.com'
        ]);

        $userId = $user->id;

        // Act - deleta o usuário
        $result = $this->repository->delete($userId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $userId
        ]);
    }

    public function test_deve_listar_todos_usuarios_do_banco()
    {
        // Arrange - cria múltiplos usuários
        User::factory()->create(['name' => 'Usuário 1', 'email' => 'user1@example.com']);
        User::factory()->create(['name' => 'Usuário 2', 'email' => 'user2@example.com']);
        User::factory()->create(['name' => 'Usuário 3', 'email' => 'user3@example.com']);

        // Act
        $users = $this->repository->all();

        // Assert
        $this->assertCount(3, $users);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $users);
    }

    public function test_deve_retornar_null_ao_buscar_usuario_inexistente()
    {
        // Act
        $user = $this->repository->find(99999);

        // Assert
        $this->assertNull($user);
    }

    public function test_deve_retornar_null_ao_atualizar_usuario_inexistente()
    {
        // Act
        $result = $this->repository->update(99999, ['name' => 'Teste']);

        // Assert
        $this->assertNull($result);
    }

    public function test_deve_retornar_false_ao_deletar_usuario_inexistente()
    {
        // Act
        $result = $this->repository->delete(99999);

        // Assert
        $this->assertFalse($result);
    }

    public function test_deve_criar_multiplos_usuarios_e_contar()
    {
        // Arrange & Act
        User::factory()->count(5)->create();

        // Assert
        $this->assertDatabaseCount('users', 5);
        
        $users = $this->repository->all();
        $this->assertCount(5, $users);
    }

    public function test_dados_do_usuario_devem_persistir_apos_atualizacao()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Carlos Silva',
            'email' => 'carlos@example.com'
        ]);

        // Act - atualiza múltiplos campos
        $this->repository->update($user->id, [
            'name' => 'Carlos Silva Atualizado',
            'email' => 'carlos.novo@example.com'
        ]);

        // Assert - busca novamente do banco para confirmar persistência
        $updatedUser = $this->repository->find($user->id);
        $this->assertEquals('Carlos Silva Atualizado', $updatedUser->name);
        $this->assertEquals('carlos.novo@example.com', $updatedUser->email);
    }
}
