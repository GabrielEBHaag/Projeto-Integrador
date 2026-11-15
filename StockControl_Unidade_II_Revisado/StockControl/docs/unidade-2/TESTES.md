# Roteiro de testes manuais — Unidade II

Registros baseados nas verificações informadas e nas capturas compartilhadas em conversa até 15/11/2026. Não marcar como aprovado um teste que não teve resultado observado.

| Caso | Procedimento | Resultado esperado | Situação verificada |
|---|---|---|---|
| T01 | Abrir aplicação sem usuários | Tela de primeiro acesso | **Aprovado** — tela exibida em captura |
| T02 | Criar usuário e fazer login | Login autorizado | **Aprovado** — usuário criado e painel exibido |
| T03 | Cadastrar produto com SKU novo | Produto listado | **Relatado pelo aluno** — confirmar persistência e captura |
| T04 | Cadastrar produto com SKU repetido | Registro duplicado recusado | **Não testado** |
| T05 | Registrar entrada de 10 unidades | Saldo aumenta 10 e histórico registra | **Movimentação relatada** — quantidade e saldo não confirmados |
| T06 | Registrar saída de 3 unidades | Saldo diminui 3 e histórico registra | **Não testado** |
| T07 | Registrar saída superior ao saldo | Operação recusada sem alterar saldo | **Não testado** |
| T08 | Conferir produto abaixo/no mínimo | Alerta no resumo | **Aprovado visualmente** — painel mostrou dois produtos no mínimo |
| T09 | Sair e acessar rota protegida | Acesso negado | **Não testado** |

**Ambiente:** XAMPP local, PHP, MariaDB em 127.0.0.1:3307.

**Evidências:** salvar capturas reais, sem senhas ou dados sensíveis, em `evidence/unidade-2/` antes da publicação.
