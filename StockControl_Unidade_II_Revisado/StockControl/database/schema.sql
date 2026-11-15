-- StockControl | Unidade II | MariaDB 10.11 / MySQL 8
-- Execute no banco stockcontrol já criado.
USE stockcontrol;

-- As tabelas categorias, fornecedores e usuarios foram criadas anteriormente.
-- Não recriá-las para preservar os dados existentes.
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao VARCHAR(255),
    sku VARCHAR(50) NOT NULL UNIQUE,
    categoria_id INT NOT NULL,
    fornecedor_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    estoque_minimo INT NOT NULL DEFAULT 5,
    preco_custo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    CONSTRAINT fk_produto_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id),
    CONSTRAINT chk_produto_quantidade CHECK (quantidade >= 0),
    CONSTRAINT chk_produto_minimo CHECK (estoque_minimo >= 0),
    CONSTRAINT chk_produto_preco CHECK (preco_custo >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo ENUM('ENTRADA','SAIDA') NOT NULL,
    quantidade INT NOT NULL,
    observacao VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mov_produto FOREIGN KEY (produto_id) REFERENCES produtos(id),
    CONSTRAINT fk_mov_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT chk_mov_quantidade CHECK (quantidade > 0),
    INDEX idx_mov_produto_data (produto_id, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
