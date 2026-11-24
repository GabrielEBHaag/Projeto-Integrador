# Unidade II — Desenvolvimento das funcionalidades básicas

**Instituição:** UCEFF  
**Curso:** Análise e Desenvolvimento de Sistemas  
**Disciplina:** Projeto Integrador  
**Projeto:** StockControl — Sistema web de gerenciamento de estoque  
**Aluno:** Gabriel Haag  
**Professor/tutor:** Maico Lovatto  
**Semestre:** 2026/2

## 1. Objetivo da etapa

Estruturar a base de dados, criar protótipos das principais telas e implementar uma versão inicial da aplicação web, mantendo o escopo estabelecido na Unidade I.

## 2. Banco de dados — DDL e DML

O banco `stockcontrol` utiliza cinco tabelas: `categorias`, `fornecedores`, `usuarios`, `produtos` e `movimentacoes`. `produtos` referencia `categorias` e `fornecedores`; `movimentacoes` referencia `produtos` e `usuarios`. Os scripts estão em `database/`:

- `base_inicial.sql`: três tabelas iniciais, apenas para instalações do zero;
- `schema.sql`: tabelas de produtos e movimentações, chaves e restrições;
- `seed.sql`: registros fictícios de categorias, fornecedores e produtos;
- `queries.sql`: consultas de estoque, alertas e histórico.

As senhas são armazenadas como hash e não há senhas de demonstração no SQL. Quantidades iniciais dos produtos fictícios são zero; entradas devem ser registradas na aplicação.

## 3. Protótipos de interface

Os arquivos SVG em `docs/unidade-2/prototipos/` apresentam os wireframes de login, resumo, produtos, fornecedores, movimentações e histórico. São representações visuais do planejamento, não capturas de telas reais. A implementação inicial está em `frontend/`.

## 4. Back-end/API

O back-end PHP usa PDO com consultas parametrizadas. As rotas retornam JSON e usam sessão para autenticação. A operação de movimentação usa transação e `SELECT ... FOR UPDATE` para bloquear o produto durante a validação do saldo, impedindo saída superior à quantidade disponível.

## 5. Relação com os requisitos da Unidade I

- RF01: login inicial implementado.
- RF02: cadastro e consulta de produtos implementados; edição e inativação pendentes.
- RF03: fornecedores existentes podem ser consultados como opções; manutenção pela interface pendente.
- RF04: categorias podem ser consultadas; cadastro pela interface pendente.
- RF05 a RF08: entradas, saídas, atualização de saldo e bloqueio de saída insuficiente implementados no código.
- RF09: painel apresenta produtos com saldo menor ou igual ao estoque mínimo.
- RF10: histórico de movimentações implementado.

## 6. Evidências e testes

**Estado da entrega:** arquivos gerados; execução e testes reais dependem de rodar no XAMPP do aluno. Não se declara validação concluída sem teste. Após execução, registrar capturas em `evidence/unidade-2/` e preencher o roteiro `TESTES.md`.

## 7. Próximas etapas

Completar manutenção de fornecedores e categorias, edição/inativação de produtos, testes de integração e documentação final para a apresentação.
