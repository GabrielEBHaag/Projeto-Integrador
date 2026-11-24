# StockControl — Unidade III

**Projeto Integrador — Análise e Desenvolvimento de Sistemas — UCEFF — 2026/2**

## 1. Objetivo

Complementar as funcionalidades do sistema web de controle de estoque iniciado nas unidades anteriores, mantendo a integração entre interface, API PHP e banco MariaDB.

## 2. Evolução do sistema

A Unidade II estabeleceu autenticação, cadastro e consulta de produtos, movimentações, resumo de estoque e histórico. Nesta unidade foram acrescentadas a edição, inativação e reativação de produtos; o cadastro, consulta, edição, inativação e reativação de fornecedores; e o cadastro e consulta de categorias pela interface.

## 3. Requisitos funcionais

| Requisito | Implementação |
|---|---|
| RF01 | Autenticação por sessão e senha com hash |
| RF02 | Cadastro, consulta, edição e alteração de status de produtos |
| RF03 | Cadastro, consulta, edição e alteração de status de fornecedores |
| RF04 | Cadastro e consulta de categorias |
| RF05 | Entrada de estoque |
| RF06 | Saída de estoque |
| RF07 | Atualização do saldo na transação de movimentação |
| RF08 | Bloqueio de saída superior ao saldo |
| RF09 | Resumo de produtos ativos com saldo igual ou inferior ao mínimo |
| RF10 | Histórico das 100 movimentações mais recentes |

A numeração segue o planejamento relatado nas etapas anteriores; conferir com o documento original na entrega final.

## 4. Integração e qualidade técnica

A interface JavaScript consome a API PHP por requisições JSON. Os catálogos são atualizados após operações de cadastro e alteração de status. A inativação lógica preserva registros e relacionamentos. As movimentações utilizam transações e bloqueio de linha, com validação de saldo e status do produto. A API emprega consultas parametrizadas, autenticação por sessão e token CSRF nas operações autenticadas de escrita. O esquema existente de cinco tabelas foi preservado, sem migração.

## 5. Verificação

O aluno confirmou o funcionamento dos testes manuais das funcionalidades complementares e de cenários integrados em XAMPP. A revisão estática verificou as rotas, a interface e os scripts SQL; a sintaxe dos três arquivos PHP e do JavaScript foi validada. O registro TESTES.md distingue os relatos de execução da inspeção de código. Não houve execução independente com banco de dados nesta revisão.

## 6. Limitações e preparação para conclusão

O sistema é destinado à demonstração acadêmica local. A configuração de banco usa root sem senha em XAMPP; para produção seriam necessários privilégios mínimos, HTTPS, proteção contra tentativas repetidas de login e testes adicionais. O CNPJ não tem validação de dígitos verificadores. A interface restringe a seleção a fornecedores ativos, mas a API não impõe essa mesma regra no cadastro/edição de produtos. O histórico exibe até 100 registros. A Unidade IV deverá consolidar a documentação, diagramas, evidências reais e apresentação conforme as orientações oficiais.

## 7. Conclusão

As funcionalidades complementares foram integradas ao sistema existente, ampliando o gerenciamento dos cadastros sem modificar o esquema do banco. Os testes manuais relatados indicam funcionamento dos fluxos exercitados. A próxima etapa é consolidar e apresentar o projeto.
