CREATE TABLE IF NOT EXISTS pedidos (
    id CHAR(36) PRIMARY KEY,
    cliente_id CHAR(36) NOT NULL,
    valor_total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) NOT NULL,
    data_criacao DATETIME NOT NULL,
    INDEX idx_cliente (cliente_id),
    INDEX idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;