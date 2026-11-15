-- Dados fictícios para demonstração; executar após schema.sql.
USE stockcontrol;
INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Acessórios e equipamentos eletrônicos'),
('Escritório', 'Materiais de escritório'),
('Limpeza', 'Produtos de limpeza')
ON DUPLICATE KEY UPDATE descricao = VALUES(descricao);

INSERT INTO fornecedores (nome, cnpj, telefone, email, endereco) VALUES
('Distribuidora Exemplo Ltda', '11222333000181', '49999990001', 'contato@exemplo.test', 'Rua Exemplo, 100'),
('Suprimentos Modelo Ltda', '22333444000192', '49999990002', 'vendas@modelo.test', 'Av. Modelo, 200')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Usuários precisam ser criados pelo PHP com password_hash().
-- Não inserir senhas em texto puro nem hashes inventados.
INSERT INTO produtos (nome, descricao, sku, categoria_id, fornecedor_id, quantidade, estoque_minimo, preco_custo)
SELECT 'Mouse USB', 'Mouse óptico com fio', 'MOU-001', c.id, f.id, 0, 5, 35.90
FROM categorias c JOIN fornecedores f ON f.cnpj='11222333000181'
WHERE c.nome='Eletrônicos'
ON DUPLICATE KEY UPDATE nome = VALUES(nome);
INSERT INTO produtos (nome, descricao, sku, categoria_id, fornecedor_id, quantidade, estoque_minimo, preco_custo)
SELECT 'Papel A4', 'Pacote com 500 folhas', 'PAP-001', c.id, f.id, 0, 10, 22.50
FROM categorias c JOIN fornecedores f ON f.cnpj='22333444000192'
WHERE c.nome='Escritório'
ON DUPLICATE KEY UPDATE nome = VALUES(nome);
