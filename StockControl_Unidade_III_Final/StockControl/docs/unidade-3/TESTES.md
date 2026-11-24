# Unidade III — Registro de testes

Ambiente relatado: Windows, XAMPP, MariaDB na porta 3307. Testes manuais realizados pelo aluno nas atualizações da Unidade III. O aluno confirmou que os grupos de testes funcionaram e que executou cenários integrados em vários produtos. Não foram fornecidos logs, capturas ou valores individuais; por isso o registro não atribui resultados numéricos específicos.

| Grupo | Procedimento proposto | Resultado esperado | Registro |
|---|---|---|---|
| TIII-01 | Editar e inativar produto | Dados atualizados; produto inativo indisponível para movimentação | Funcionamento confirmado pelo aluno |
| TIII-02 | Reativar produto | Produto volta a ficar disponível; saldo e histórico preservados | Grupo confirmado pelo aluno |
| TIII-03 | Cadastrar, editar, inativar e reativar fornecedor | Cadastro e status atualizados | Grupo confirmado pelo aluno |
| TIII-04 | Cadastrar categoria e selecionar no produto | Categoria disponível no catálogo | Grupo confirmado pelo aluno |
| TIII-05 | Tentar cadastrar categoria duplicada | Operação recusada | Grupo confirmado pelo aluno |
| TIII-06 | Integrar produto, categoria, fornecedor, entrada, saída, bloqueio de saldo insuficiente e histórico | Operações válidas registradas; saída inválida recusada | Cenários integrados confirmados pelo aluno, sem detalhamento individual |

## Verificação estática em 24/11/2026

- `php -l backend/api/index.php`: sem erros de sintaxe.
- `php -l backend/config/db.php`: sem erros de sintaxe.
- `php -l frontend/index.php`: sem erros de sintaxe.
- `node --check frontend/js/app.js`: sem erros de sintaxe.

A verificação de sintaxe não substitui testes de integração com MariaDB. Não foram executados testes automatizados de segurança, concorrência ou desempenho. Capturas reais serão reunidas na etapa final; não foram fabricadas evidências.
