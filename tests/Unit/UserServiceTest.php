<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Models\User;
use Mockery;

class UserServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ====================================
    // TESTES SIMPLES DE UNIDADE
    // ====================================

    public function test_deve_retornar_nome_do_usuario()
    {
        // cria o mock do repositório
        $mockRepository = Mockery::mock(UserRepository::class);

        // define comportamento esperado
        $mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn((object)['name' => 'Alex']);

        // injeta o mock no serviço
        $service = new UserService($mockRepository);

        // executa o método testado
        $result = $service->getUserName(1);

        // valida o resultado
        $this->assertEquals('Alex', $result);
    }

    public function test_deve_retornar_mensagem_quando_usuario_nao_existir()
    {
        $mockRepository = Mockery::mock(UserRepository::class);
        $mockRepository
            ->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new UserService($mockRepository);
        $result = $service->getUserName(999);

        $this->assertEquals('Usuário não encontrado', $result);
    }

    // ====================================
    // TESTES COM MOCK - CREATE
    // ====================================

    public function test_deve_criar_usuario_com_sucesso()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'senha123'
        ];

        $expectedUser = (object)[
            'id' => 1,
            'name' => 'João Silva',
            'email' => 'joao@example.com'
        ];

        // Mock espera que create seja chamado uma vez com os dados corretos
        $mockRepository
            ->shouldReceive('create')
            ->once()
            ->with($userData)
            ->andReturn($expectedUser);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->createUser($userData);

        // Assert
        $this->assertEquals('João Silva', $result->name);
        $this->assertEquals('joao@example.com', $result->email);
    }

    public function test_deve_lancar_excecao_ao_criar_usuario_sem_nome()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome e email são obrigatórios');

        $mockRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($mockRepository);

        // Tenta criar usuário sem nome
        $service->createUser([
            'email' => 'teste@example.com'
        ]);
    }

    public function test_deve_lancar_excecao_ao_criar_usuario_sem_email()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome e email são obrigatórios');

        $mockRepository = Mockery::mock(UserRepository::class);
        $service = new UserService($mockRepository);

        // Tenta criar usuário sem email
        $service->createUser([
            'name' => 'João Silva'
        ]);
    }

    // ====================================
    // TESTES COM MOCK - UPDATE
    // ====================================

    public function test_deve_atualizar_usuario_existente()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $userId = 1;
        $updateData = ['name' => 'João Atualizado'];

        $existingUser = (object)[
            'id' => 1,
            'name' => 'João Silva',
            'email' => 'joao@example.com'
        ];

        $updatedUser = (object)[
            'id' => 1,
            'name' => 'João Atualizado',
            'email' => 'joao@example.com'
        ];

        // Mock: find deve retornar o usuário existente
        $mockRepository
            ->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn($existingUser);

        // Mock: update deve retornar o usuário atualizado
        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->with($userId, $updateData)
            ->andReturn($updatedUser);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->updateUser($userId, $updateData);

        // Assert
        $this->assertEquals('João Atualizado', $result->name);
    }

    public function test_deve_retornar_null_ao_atualizar_usuario_inexistente()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $userId = 999;

        // Mock: find não encontra o usuário
        $mockRepository
            ->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(null);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->updateUser($userId, ['name' => 'Teste']);

        // Assert
        $this->assertNull($result);
    }

    // ====================================
    // TESTES COM MOCK - DELETE
    // ====================================

    public function test_deve_deletar_usuario_existente()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $userId = 1;

        // Mock: delete retorna true (sucesso)
        $mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(true);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->deleteUser($userId);

        // Assert
        $this->assertTrue($result);
    }

    public function test_deve_retornar_false_ao_deletar_usuario_inexistente()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        $userId = 999;

        // Mock: delete retorna false (usuário não encontrado)
        $mockRepository
            ->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(false);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->deleteUser($userId);

        // Assert
        $this->assertFalse($result);
    }

    // ====================================
    // TESTES COM MOCK - LIST ALL
    // ====================================

    public function test_deve_listar_todos_usuarios()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);
        
        $expectedUsers = [
            (object)['id' => 1, 'name' => 'João', 'email' => 'joao@example.com'],
            (object)['id' => 2, 'name' => 'Maria', 'email' => 'maria@example.com'],
            (object)['id' => 3, 'name' => 'Pedro', 'email' => 'pedro@example.com']
        ];

        // Mock: all retorna lista de usuários
        $mockRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedUsers);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->getAllUsers();

        // Assert
        $this->assertCount(3, $result);
        $this->assertEquals('João', $result[0]->name);
        $this->assertEquals('Maria', $result[1]->name);
        $this->assertEquals('Pedro', $result[2]->name);
    }

    public function test_deve_retornar_lista_vazia_quando_nao_ha_usuarios()
    {
        // Arrange
        $mockRepository = Mockery::mock(UserRepository::class);

        // Mock: all retorna array vazio
        $mockRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn([]);

        // Act
        $service = new UserService($mockRepository);
        $result = $service->getAllUsers();

        // Assert
        $this->assertCount(0, $result);
        $this->assertIsArray($result);
    }
}
