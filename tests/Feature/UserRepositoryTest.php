<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Repositories\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase; //Garante que o banco de dados esteja limpo e sincronizado antes de cada teste ser executado.

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
            'name' => 'Zeca Pagodinho',
            'email' => 'zeca@cajutec.com',
            'password' => bcrypt('senha123')
        ];

        // Act
        $user = $this->repository->create($userData);

        // Assert
        $this->assertDatabaseHas('users', [
            'name' => 'Zeca Pagodinho',
            'email' => 'zeca@cajutec.com'
        ]);

        $this->assertEquals('Zeca Pagodinho', $user->name);
        $this->assertEquals('zeca@cajutec.com', $user->email);
        $this->assertNotNull($user->id);
    }

    public function test_deve_buscar_usuario_por_id_no_banco()
    {
        // Arrange - cria um usuário no banco
        $createdUser = User::factory()->create([
            'name' => 'Regina George',
            'email' => 'regina@cajutec.com'
        ]);

        // Act - busca o usuário pelo ID
        $foundUser = $this->repository->find($createdUser->id);

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals('Regina George', $foundUser->name);
        $this->assertEquals('regina@cajutec.com', $foundUser->email);
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

    // ====================================
    // TESTES COM DATA PROVIDERS
    // Os data providers em PHPUnit são configurados através de Attributes (PHP 8+)
    // Sintaxe moderna: #[DataProvider('nomeDoProvider')]
    // ====================================

    /**
     * Testa criação de múltiplos usuários com dados diferentes
     */
    #[DataProvider('usuariosParaCriarProvider')]
    public function test_deve_criar_diferentes_usuarios_no_banco($nome, $email)
    {
        // Arrange
        $userData = [
            'name' => $nome,
            'email' => $email,
            'password' => bcrypt('senha123')
        ];

        // Act
        $user = $this->repository->create($userData);

        // Assert - Valida que foi criado corretamente
        $this->assertNotNull($user);
        $this->assertEquals($nome, $user->name);
        $this->assertEquals($email, $user->email);
        $this->assertNotNull($user->id);
        
        // Valida que está persistido no banco
        $this->assertDatabaseHas('users', [
            'name' => $nome,
            'email' => $email
        ]);
    }

    /**
     * Fornece diferentes conjuntos de dados de usuários para testar criação
     */
    public static function usuariosParaCriarProvider(): array
    {
        return [
            'Cantor de Samba 1' => ['Zeca Pagodinho', 'zeca@samba.com'],
            'Cantor de Samba 2' => ['Beth Carvalho', 'beth@samba.com'],
            'Cantor de Samba 3' => ['Alcione', 'alcione@samba.com'],
            'Cantor de Samba 4' => ['Jorge Aragão', 'jorge@samba.com'],
            'Cantor de Samba 5' => ['Martinho da Vila', 'martinho@samba.com'],
            'Nome com acentos' => ['José da Silva Açúcar', 'jose.acucar@test.com'],
            'Nome composto longo' => ['Maria das Graças Santos Oliveira', 'maria.gracas@email.com'],
            'Email corporativo' => ['João Santos', 'joao.santos@empresa.com.br'],
        ];
    }

    /**
     * Testa atualização de diferentes campos do usuário
     */
    #[DataProvider('camposParaAtualizarProvider')]
    public function test_deve_atualizar_diferentes_campos_do_usuario($campo, $novoValor)
    {
        // Arrange - Cria um usuário base
        $user = User::factory()->create([
            'name' => 'Nome Original',
            'email' => 'original@test.com'
        ]);

        // Act - Atualiza o campo específico
        $updatedUser = $this->repository->update($user->id, [
            $campo => $novoValor
        ]);

        // Assert - Valida que foi atualizado
        $this->assertNotNull($updatedUser);
        $this->assertEquals($novoValor, $updatedUser->$campo);
        
        // Valida persistência no banco
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            $campo => $novoValor
        ]);
    }

    /**
     * Fornece diferentes campos e valores para testar atualização
     */
    public static function camposParaAtualizarProvider(): array
    {
        return [
            'Atualiza nome simples' => ['name', 'João Atualizado'],
            'Atualiza nome com acentos' => ['name', 'José Ãçúcar Atualizado'],
            'Atualiza nome longo' => ['name', 'Maria das Graças Santos Oliveira Atualizado'],
            'Atualiza email simples' => ['email', 'novo@email.com'],
            'Atualiza email corporativo' => ['email', 'usuario@empresa.com.br'],
            'Atualiza email com subdominio' => ['email', 'user@mail.subdomain.com'],
        ];
    }

    /**
     * Testa criação de diferentes quantidades de usuários
     */
    #[DataProvider('quantidadesUsuariosProvider')]
    public function test_deve_criar_quantidade_especifica_de_usuarios($quantidade)
    {
        // Arrange & Act - Cria N usuários
        User::factory()->count($quantidade)->create();

        // Assert - Valida a quantidade correta
        $this->assertDatabaseCount('users', $quantidade);
        
        $users = $this->repository->all();
        $this->assertCount($quantidade, $users);
    }

    /**
     * Fornece diferentes quantidades para testar criação em massa
     */
    public static function quantidadesUsuariosProvider(): array
    {
        return [
            '1 usuário' => [1],
            '3 usuários' => [3],
            '5 usuários' => [5],
            '10 usuários' => [10],
            '20 usuários' => [20],
        ];
    }

    /**
     * Testa busca de usuários inexistentes com diferentes IDs
     */
    #[DataProvider('idsInexistentesProvider')]
    public function test_deve_retornar_null_para_diferentes_ids_inexistentes($idInexistente)
    {
        // Act - Tenta buscar usuário que não existe
        $user = $this->repository->find($idInexistente);

        // Assert
        $this->assertNull($user);
    }

    /**
     * Fornece diferentes IDs inexistentes para testar
     */
    public static function idsInexistentesProvider(): array
    {
        return [
            'ID muito alto' => [99999],
            'ID médio' => [5000],
            'ID negativo' => [-1],
            'ID zero' => [0],
        ];
    }

    /**
     * Testa diferentes cenários de nomes de usuários
     */
    #[DataProvider('nomesUsuariosProvider')]
    public function test_deve_persistir_nomes_com_diferentes_formatos($nome)
    {
        // Arrange & Act
        $user = User::factory()->create([
            'name' => $nome,
            'email' => 'test' . uniqid() . '@example.com'
        ]);

        // Assert - Valida que o nome foi salvo corretamente
        $this->assertEquals($nome, $user->name);
        $this->assertDatabaseHas('users', ['name' => $nome]);
        
        // Busca do banco para garantir persistência
        $foundUser = $this->repository->find($user->id);
        $this->assertEquals($nome, $foundUser->name);
    }

    /**
     * Fornece diferentes formatos de nomes para testar
     */
    public static function nomesUsuariosProvider(): array
    {
        return [
            'Nome simples' => ['João'],
            'Nome composto' => ['João Silva'],
            'Nome com três palavras' => ['João da Silva'],
            'Nome com acentos' => ['José Açúcar'],
            'Nome com til' => ['João São Paulo'],
            'Nome com cedilha' => ['Conceição Santos'],
            'Nome completo longo' => ['Maria das Graças Santos Oliveira Costa'],
            'Nome com apóstrofo' => ["D'Angelo Silva"],
        ];
    }

    /**
     * Testa atualização seguida de busca para diferentes usuários
     */
    #[DataProvider('dadosAtualizacaoCompletaProvider')]
    public function test_deve_atualizar_e_persistir_dados_completos($nomeOriginal, $emailOriginal, $nomeNovo, $emailNovo)
    {
        // Arrange - Cria usuário com dados originais
        $user = User::factory()->create([
            'name' => $nomeOriginal,
            'email' => $emailOriginal
        ]);

        // Act - Atualiza ambos os campos
        $this->repository->update($user->id, [
            'name' => $nomeNovo,
            'email' => $emailNovo
        ]);

        // Assert - Busca do banco e valida persistência
        $updatedUser = $this->repository->find($user->id);
        
        $this->assertEquals($nomeNovo, $updatedUser->name);
        $this->assertEquals($emailNovo, $updatedUser->email);
        
        // Valida que dados antigos não existem mais
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => $nomeOriginal
        ]);
        
        // Valida que novos dados existem
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $nomeNovo,
            'email' => $emailNovo
        ]);
    }

    /**
     * Fornece pares de dados originais e atualizados
     */
    public static function dadosAtualizacaoCompletaProvider(): array
    {
        return [
            'Atualização simples' => [
                'Pedro Silva',
                'pedro@old.com',
                'Pedro Santos',
                'pedro@new.com'
            ],
            'Mudança completa' => [
                'Maria Costa',
                'maria.costa@empresa.com',
                'Maria Costa Santos',
                'maria.santos@novaempresa.com.br'
            ],
            'Com acentos' => [
                'José da Silva',
                'jose@test.com',
                'José Açúcar Santos',
                'jose.acucar@novo.com'
            ],
        ];
    }

    /**
     * Testa exclusão de múltiplos usuários
     */
    #[DataProvider('usuariosParaDeletarProvider')]
    public function test_deve_deletar_usuarios_com_diferentes_dados($nome, $email)
    {
        // Arrange - Cria usuário
        $user = User::factory()->create([
            'name' => $nome,
            'email' => $email
        ]);
        
        $userId = $user->id;

        // Act - Deleta o usuário
        $result = $this->repository->delete($userId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', [
            'id' => $userId,
            'name' => $nome,
            'email' => $email
        ]);
        
        // Valida que não pode mais encontrar
        $deletedUser = $this->repository->find($userId);
        $this->assertNull($deletedUser);
    }

    /**
     * Fornece dados de usuários para testar exclusão
     */
    public static function usuariosParaDeletarProvider(): array
    {
        return [
            'Usuário 1' => ['Ana Silva', 'ana@delete.com'],
            'Usuário 2' => ['Bruno Costa', 'bruno@delete.com'],
            'Usuário 3' => ['Carla Santos', 'carla@delete.com'],
        ];
    }
}
