# StockControl — Projeto Integrador (UCEFF, 2026/2)

Sistema web acadêmico para controle de estoque de uma pequena empresa. Tecnologias: HTML, CSS, JavaScript, PHP 8 e MariaDB/MySQL.

## Executar no Windows com XAMPP

1. Copie **toda a pasta** `StockControl` para `C:\xampp\htdocs\stockcontrol` (renomeie a pasta para `stockcontrol`).
2. Inicie Apache e MySQL no XAMPP. O projeto está configurado para banco em `127.0.0.1:3307`.
3. Se o banco estiver vazio, execute, nesta ordem, `database/base_inicial.sql`, `database/schema.sql` e `database/seed.sql` no phpMyAdmin. Se o banco já funciona, não reimporte os scripts.
4. Acesse `http://localhost/stockcontrol/frontend/` no navegador.
5. No primeiro acesso, crie um usuário com senha de pelo menos 10 caracteres. Depois faça login.
6. Cadastre produtos, registre entrada e saída e confira o resumo e o histórico.

## Funcionalidades desta versão

- Primeiro usuário e login com senha protegida por `password_hash()` e sessões PHP.
- Cadastro, consulta, edição, inativação e reativação de produtos.
- Cadastro, consulta, edição, inativação e reativação de fornecedores.
- Cadastro e consulta de categorias, com nome único.
- Registro de entradas e saídas em transação, com bloqueio de linha e validação do saldo.
- Resumo de estoque mínimo e histórico das 100 movimentações mais recentes.
- Consultas SQL e protótipos de tela em `docs/unidade-2/prototipos/`.

## Limitações conhecidas

- CNPJ opcional: validação de comprimento, sem conferência dos dígitos verificadores.
- Histórico limitado às 100 movimentações mais recentes.
- A inativação de fornecedor preserva os vínculos e o histórico existentes.
- Esta é uma aplicação de demonstração local, não pronta para publicação na internet: falta configurar usuário de banco com privilégios mínimos, HTTPS, proteção contra tentativas de login e testes abrangentes.
- Não envie credenciais ou arquivos locais com senhas para o GitHub. A configuração de banco incluída é exclusiva para ambiente XAMPP local de demonstração.

## API

Base: `/stockcontrol/backend/api/index.php?action=...`

| Método | action | Função |
|---|---|---|
| GET | setup-status | Verifica se precisa criar primeiro usuário |
| POST | setup | Cria primeiro usuário, apenas se tabela `usuarios` vazia |
| POST | login | Autentica e abre sessão |
| GET | me | Identifica usuário autenticado |
| POST | logout | Encerra sessão |
| GET | catalogos | Lista categorias e fornecedores ativos |
| GET/POST | produtos | Lista ou cadastra produto |
| POST | produto-atualizar | Edita produto ativo |
| POST | produto-inativar / produto-reativar | Altera status do produto |
| GET/POST | fornecedores | Lista ou cadastra fornecedor |
| POST | fornecedor-atualizar | Edita fornecedor ativo |
| POST | fornecedor-inativar / fornecedor-reativar | Altera status do fornecedor |
| GET/POST | categorias | Lista ou cadastra categoria |
| GET/POST | movimentacoes | Lista ou registra movimentação |

Para POST autenticado, enviar cabeçalho `X-CSRF-Token` retornado pelo login. Respostas JSON.

## Organização

`database/` SQL; `backend/` PHP; `frontend/` interface; `docs/` protótipos e documentação; `evidence/` capturas reais após executar e testar.

## Unidade III — funcionalidades complementares

Consulte [relatório](docs/unidade-3/RELATORIO.md), [registro de testes](docs/unidade-3/TESTES.md) e [progresso](docs/unidade-3/PROGRESSO.md). A atualização não exige migração do banco.
