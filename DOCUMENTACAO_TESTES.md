# Atividade de Testes com PHPUnit - Documentação

## ⚡ Comandos Rápidos (Quick Reference)

### 🧪 Executar Testes

```bash
# Executar TODOS os testes
php artisan test

# Executar com saída detalhada
php artisan test --testdox

# Executar apenas testes unitários (com mocks)
php artisan test --testsuite=Unit

# Executar apenas testes de integração (com banco)
php artisan test --testsuite=Feature

# Executar um arquivo de teste específico
php artisan test tests/Unit/UserServiceTest.php
php artisan test tests/Feature/UserRepositoryTest.php

# Executar um teste específico por nome
php artisan test --filter=test_deve_criar_usuario_no_banco

# Executar com cobertura de código
php artisan test --coverage

# Executar e parar no primeiro erro
php artisan test --stop-on-failure
```

### 🗄️ Gerenciar Banco de Dados de Testes

```bash
# Rodar migrações no banco de desenvolvimento
php artisan migrate

# Rodar migrações no banco de testes
php artisan migrate --env=testing

# Limpar e recriar banco de testes
php artisan migrate:fresh --env=testing

# Popular banco de testes com dados
php artisan db:seed --env=testing

# Verificar qual banco está sendo usado (via Tinker)
php artisan tinker --env=testing
>>> DB::connection()->getDatabaseName();
```

### 📊 Análise de Testes

```bash
# Ver lista de testes disponíveis
php artisan test --list-tests

# Executar em modo verboso
php artisan test -v
php artisan test -vv
php artisan test -vvv

# Gerar relatório de cobertura HTML
php artisan test --coverage-html coverage-report

# Executar testes em paralelo (mais rápido)
php artisan test --parallel
```

### 🔧 Comandos Úteis do Artisan

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rodar aplicação em desenvolvimento
php artisan serve

# Ver rotas disponíveis
php artisan route:list

# Criar nova migração
php artisan make:migration create_nome_tabela

# Criar novo teste
php artisan make:test NomeDoTest
php artisan make:test NomeDoTest --unit
```

### 🐛 Debug e Troubleshooting

```bash
# Ver logs da aplicação
tail -f storage/logs/laravel.log

# Limpar todos os caches
php artisan optimize:clear

# Verificar configuração do banco (via Tinker)
php artisan tinker
>>> config('database.default');
>>> config('database.connections.mysql.database');
```

---

## 📋 Resumo da Implementação

Esta atividade implementa testes completos utilizando PHPUnit no framework Laravel, cobrindo todos os requisitos solicitados:

### ✅ Requisitos Atendidos

1. **Testes simples de unidade** ✓
2. **Uso de mock** ✓
3. **Testes que envolvam salvar informações no banco de dados** ✓

---

## 📁 Estrutura de Arquivos

### Arquivos de Produção
- `app/Services/UserService.php` - Serviço com lógica de negócio
- `app/Repositories/UserRepository.php` - Repositório para acesso ao banco
- `app/Models/User.php` - Model do Laravel (já existente)

### Arquivos de Teste
- `tests/Unit/UserServiceTest.php` - Testes unitários com mocks
- `tests/Feature/UserRepositoryTest.php` - Testes de integração com banco

---

## 🧪 Tipos de Testes Implementados

### 1. Testes Simples de Unidade (UserServiceTest.php)

**Localização:** `tests/Unit/UserServiceTest.php`

Testes que validam lógica isolada sem dependências externas:

- ✓ `test_deve_retornar_nome_do_usuario()` - Testa busca de nome
- ✓ `test_deve_retornar_mensagem_quando_usuario_nao_existir()` - Testa caso de erro
- ✓ `test_deve_lancar_excecao_ao_criar_usuario_sem_nome()` - Testa validação
- ✓ `test_deve_lancar_excecao_ao_criar_usuario_sem_email()` - Testa validação

### 2. Testes com Mock (UserServiceTest.php)

**Localização:** `tests/Unit/UserServiceTest.php`

Utiliza **Mockery** para simular o comportamento do repositório:

#### Testes de Criação (CREATE)
- ✓ `test_deve_criar_usuario_com_sucesso()` - Mock de criação bem-sucedida
- ✓ Valida que o repositório é chamado com dados corretos

#### Testes de Atualização (UPDATE)
- ✓ `test_deve_atualizar_usuario_existente()` - Mock de atualização
- ✓ `test_deve_retornar_null_ao_atualizar_usuario_inexistente()` - Mock de falha

#### Testes de Exclusão (DELETE)
- ✓ `test_deve_deletar_usuario_existente()` - Mock de exclusão
- ✓ `test_deve_retornar_false_ao_deletar_usuario_inexistente()` - Mock de falha

#### Testes de Listagem (READ)
- ✓ `test_deve_listar_todos_usuarios()` - Mock retornando lista
- ✓ `test_deve_retornar_lista_vazia_quando_nao_ha_usuarios()` - Mock de lista vazia

**Total:** 11 testes unitários com mocks

### 3. Testes com Banco de Dados (UserRepositoryTest.php)

**Localização:** `tests/Feature/UserRepositoryTest.php`

Utiliza **RefreshDatabase** para testar operações reais no banco:

#### Características
- Usa banco de dados SQLite em memória para testes
- Cada teste roda em uma transação isolada
- Banco é resetado após cada teste

#### Testes Implementados
- ✓ `test_deve_criar_usuario_no_banco_de_dados()` - Insere no banco
- ✓ `test_deve_buscar_usuario_por_id_no_banco()` - Consulta no banco
- ✓ `test_deve_atualizar_usuario_no_banco_de_dados()` - Atualiza no banco
- ✓ `test_deve_deletar_usuario_do_banco_de_dados()` - Deleta do banco
- ✓ `test_deve_listar_todos_usuarios_do_banco()` - Lista múltiplos registros
- ✓ `test_deve_retornar_null_ao_buscar_usuario_inexistente()` - Testa caso de erro
- ✓ `test_deve_retornar_null_ao_atualizar_usuario_inexistente()` - Testa caso de erro
- ✓ `test_deve_retornar_false_ao_deletar_usuario_inexistente()` - Testa caso de erro
- ✓ `test_deve_criar_multiplos_usuarios_e_contar()` - Testa criação em massa
- ✓ `test_dados_do_usuario_devem_persistir_apos_atualizacao()` - Testa persistência

**Total:** 10 testes básicos de integração com banco

#### Testes com Data Providers (Parametrizados)
- ✓ `test_deve_criar_diferentes_usuarios_no_banco()` - 8 execuções com dados diferentes
- ✓ `test_deve_atualizar_diferentes_campos_do_usuario()` - 6 execuções testando nome e email
- ✓ `test_deve_criar_quantidade_especifica_de_usuarios()` - 5 execuções com quantidades variadas
- ✓ `test_deve_retornar_null_para_diferentes_ids_inexistentes()` - 4 execuções com IDs inválidos
- ✓ `test_deve_persistir_nomes_com_diferentes_formatos()` - 8 execuções com formatos variados
- ✓ `test_deve_atualizar_e_persistir_dados_completos()` - 3 execuções com atualizações completas
- ✓ `test_deve_deletar_usuarios_com_diferentes_dados()` - 3 execuções testando exclusão

**Total:** 37 testes parametrizados (usando Data Providers)

---

## 🎯 Conceitos e Técnicas Utilizadas

### Mocking (Mockery)
```php
$mockRepository = Mockery::mock(UserRepository::class);
$mockRepository
    ->shouldReceive('find')
    ->once()
    ->with(1)
    ->andReturn((object)['name' => 'Alex']);
```

**Benefícios:**
- Isola a unidade testada
- Não depende de banco de dados
- Testes muito rápidos
- Valida interações entre objetos

### Database Testing (RefreshDatabase)
```php
use RefreshDatabase;

public function test_deve_criar_usuario_no_banco()
{
    $user = $this->repository->create([...]);
    $this->assertDatabaseHas('users', [...]);
}
```

**Benefícios:**
- Testa integração real
- Valida queries SQL
- Garante comportamento correto do ORM
- Detecta problemas de migração

### Data Providers (Testes Parametrizados)
```php
/**
 * @dataProvider usuariosParaCriarProvider
 */
public function test_deve_criar_diferentes_usuarios($nome, $email)
{
    $user = $this->repository->create([
        'name' => $nome,
        'email' => $email,
        'password' => bcrypt('senha')
    ]);
    
    $this->assertEquals($nome, $user->name);
}

public static function usuariosParaCriarProvider(): array
{
    return [
        'Usuário 1' => ['João Silva', 'joao@test.com'],
        'Usuário 2' => ['Maria Santos', 'maria@test.com'],
        'Usuário 3' => ['Pedro Costa', 'pedro@test.com'],
    ];
}
```

**Benefícios:**
- Evita duplicação de código (DRY)
- Testa múltiplos cenários facilmente
- Facilita adicionar novos casos de teste
- Melhora legibilidade e organização
- Identifica exatamente qual conjunto de dados falhou

### Assertions Utilizadas
- `assertEquals()` - Compara valores
- `assertNull()` / `assertNotNull()` - Valida nulos
- `assertTrue()` / `assertFalse()` - Valida booleanos
- `assertCount()` - Conta elementos
- `assertDatabaseHas()` - Verifica existência no banco
- `assertDatabaseMissing()` - Verifica ausência no banco
- `assertDatabaseCount()` - Conta registros no banco
- `expectException()` - Valida exceções

---

## 🚀 Como Executar os Testes

### Executar todos os testes
```bash
php artisan test
```

### Executar apenas testes unitários
```bash
php artisan test --testsuite=Unit
```

### Executar apenas testes de integração
```bash
php artisan test --testsuite=Feature
```

### Executar um arquivo específico
```bash
php artisan test tests/Unit/UserServiceTest.php
php artisan test tests/Feature/UserRepositoryTest.php
```

### Executar um teste específico
```bash
php artisan test --filter=test_deve_criar_usuario_no_banco
```

### Executar com detalhes (testdox)
```bash
php artisan test --testdox
```

### Executar com cobertura de código
```bash
php artisan test --coverage
```

---

## 📊 Resultados

### Total de Testes: 60
- ✅ 60 testes passaram
- ✅ 158 asserções validadas
- ⏱️ Duração: ~1 segundo

### Distribuição:
- **Testes Unitários (Unit):** 12 testes
  - 1 teste de exemplo
  - 11 testes do UserService com mocks
  
- **Testes de Integração (Feature):** 48 testes
  - 1 teste de exemplo
  - 10 testes básicos do UserRepository com banco
  - 37 testes com Data Providers (parametrizados)

---

## 🎓 Conceitos Aprendidos

1. **Test-Driven Development (TDD)**
   - Escrever testes antes ou junto com o código
   - Garantir cobertura adequada

2. **Mocking**
   - Simular dependências externas
   - Isolar unidades de teste
   - Usar Mockery no PHP

3. **Database Testing**
   - Trait RefreshDatabase
   - Factories do Laravel
   - Assertions de banco de dados

4. **Arrange-Act-Assert (AAA)**
   - Estruturar testes de forma clara
   - Separar preparação, execução e validação

5. **Data Providers (Parametrização)**
   - Executar mesmo teste com dados diferentes
   - Evitar duplicação de código
   - Documentar casos de uso claramente
   - Facilitar manutenção e expansão

6. **SOLID Principles**
   - Dependency Injection
   - Single Responsibility
   - Interface Segregation

---

## 💡 Boas Práticas Aplicadas

✅ Nomes descritivos de testes em português  
✅ Organização em seções com comentários  
✅ Um assert por conceito (geralmente)  
✅ Testes independentes entre si  
✅ Uso de factories para dados de teste  
✅ Validação de casos de sucesso e erro  
✅ Isolamento com mocks quando apropriado  
✅ Testes de integração quando necessário  

---

## 📝 Arquitetura do Código

```
Controller (futuro)
    ↓
UserService (lógica de negócio)
    ↓
UserRepository (acesso a dados)
    ↓
User Model (Eloquent ORM)
    ↓
Database
```

### Camada de Teste:
- **Unit Tests:** Testam UserService com mocks do Repository
- **Feature Tests:** Testam UserRepository com banco real

---

## 🔧 Tecnologias Utilizadas

- **Framework:** Laravel 11.x
- **Ferramenta de Teste:** PHPUnit 11.x
- **Biblioteca de Mock:** Mockery
- **Banco de Dados (Teste):** MySQL (teste_mocker_testing)
- **PHP:** 8.x
- **Técnicas Avançadas:** Data Providers (Testes Parametrizados)

---

## ✨ Conclusão

Esta implementação demonstra domínio completo dos requisitos da atividade e vai além:

1. ✅ **Testes simples de unidade** - Validam lógica isolada (12 testes)
2. ✅ **Uso de mock** - Simulam dependências com Mockery (11 testes)
3. ✅ **Testes com banco de dados** - Validam persistência real (47 testes)
4. ✅ **Data Providers** - Testes parametrizados avançados (37 testes)

**Todos os 60 testes estão passando** (158 asserções), demonstrando código robusto, bem testado e seguindo as melhores práticas da indústria!
