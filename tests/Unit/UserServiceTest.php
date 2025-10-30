<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use Mockery;

class UserServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

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
}
