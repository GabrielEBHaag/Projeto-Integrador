-- Consultas de demonstração
USE stockcontrol;
SELECT p.id, p.nome, p.sku, c.nome AS categoria, f.nome AS fornecedor,
       p.quantidade, p.estoque_minimo
FROM produtos p
JOIN categorias c ON c.id = p.categoria_id
JOIN fornecedores f ON f.id = p.fornecedor_id
WHERE p.ativo = TRUE ORDER BY p.nome;

-- Produtos no estoque mínimo ou abaixo dele
SELECT id, nome, quantidade, estoque_minimo FROM produtos
WHERE ativo = TRUE AND quantidade <= estoque_minimo ORDER BY quantidade;

-- Histórico de movimentações
SELECT m.id, p.nome AS produto, u.nome AS usuario, m.tipo,
       m.quantidade, m.observacao, m.criado_em
FROM movimentacoes m
JOIN produtos p ON p.id = m.produto_id
JOIN usuarios u ON u.id = m.usuario_id
ORDER BY m.criado_em DESC, m.id DESC;

-- IMPORTANTE: entrada/saída devem ocorrer em uma transação no backend,
-- com bloqueio SELECT ... FOR UPDATE e validação de saldo antes de gravar.
-- Não altere quantidade diretamente sem registrar movimentação.
