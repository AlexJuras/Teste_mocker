# Atividade de Testes com PHPUnit - Documentação

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

**Total:** 10 testes de integração com banco

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

### Executar com cobertura de código
```bash
php artisan test --coverage
```

---

## 📊 Resultados

### Total de Testes: 23
- ✅ 23 testes passaram
- ✅ 41 asserções validadas
- ⏱️ Duração: ~4 segundos

### Distribuição:
- **Testes Unitários (Unit):** 12 testes
  - 1 teste de exemplo
  - 11 testes do UserService com mocks
  
- **Testes de Integração (Feature):** 11 testes
  - 1 teste de exemplo
  - 10 testes do UserRepository com banco

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

5. **SOLID Principles**
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
- **Banco de Dados (Teste):** SQLite in-memory
- **PHP:** 8.x

---

## ✨ Conclusão

Esta implementação demonstra domínio completo dos três requisitos da atividade:

1. ✅ **Testes simples de unidade** - Validam lógica isolada
2. ✅ **Uso de mock** - Simulam dependências com Mockery
3. ✅ **Testes com banco de dados** - Validam persistência real

Todos os 23 testes estão passando, demonstrando código funcional e bem testado!
