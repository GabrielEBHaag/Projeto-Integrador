# StockControl — Projeto Integrador (UCEFF, 2026/2)

Sistema web acadêmico para controle de estoque de uma pequena empresa. Tecnologias: HTML, CSS, JavaScript, PHP 8 e MariaDB/MySQL.

## Executar no Windows com XAMPP

1. Copie a pasta **`StockControl`** para `C:\xampp\htdocs\stockcontrol` (renomeie para `stockcontrol`, se necessário).
2. Inicie Apache e MySQL no XAMPP. O projeto está configurado para banco em `127.0.0.1:3307`.
3. No phpMyAdmin, use o banco `stockcontrol` já criado. Se ainda não criou as tabelas, execute `database/schema.sql` **depois de criar** as tabelas `categorias`, `fornecedores`, `usuarios` (definições abaixo). Depois execute `database/seed.sql`. Se já executou esses scripts, **não é necessário repetir**.
4. Acesse `http://localhost/stockcontrol/frontend/` no navegador.
5. No primeiro acesso, crie um usuário com senha de pelo menos 10 caracteres. Depois faça login.
6. Cadastre produtos, registre entrada e saída e confira o resumo e o histórico.

### Tabelas criadas anteriormente no phpMyAdmin

Se estiver iniciando em um banco vazio, execute primeiro `database/base_inicial.sql`, depois `schema.sql` e `seed.sql`.

## Funcionalidades desta versão

- Primeiro usuário e login com senha protegida por `password_hash()` e sessões PHP.
- Consulta e cadastro de produtos, com categoria e fornecedor.
- Registro de entradas e saídas em transação, com bloqueio de linha e validação do saldo.
- Resumo de estoque mínimo e histórico das 100 movimentações mais recentes.
- Consultas SQL e protótipos de tela em `docs/unidade-2/prototipos/`.

## Limitações conhecidas

- Cadastro/edição/inativação de fornecedores e edição/inativação de produtos ainda não estão disponíveis pela interface.
- Cadastro de categorias é realizado por SQL nesta etapa.
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
| GET/POST | movimentacoes | Lista ou registra movimentação |

Para POST autenticado, enviar cabeçalho `X-CSRF-Token` retornado pelo login. Respostas JSON.

## Organização

`database/` SQL; `backend/` PHP; `frontend/` interface; `docs/` protótipos e documentação; `evidence/` capturas reais após executar e testar.
