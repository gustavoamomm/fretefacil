-- FreteFácil - Estrutura do banco de dados
-- MySQL / MariaDB
--
-- Este script cria a estrutura necessária para executar o projeto
-- localmente. Não contém credenciais reais.

CREATE DATABASE IF NOT EXISTS fretefacil_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE fretefacil_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS tb_movimentacao;
DROP TABLE IF EXISTS tb_pagamento;
DROP TABLE IF EXISTS tb_carteira;
DROP TABLE IF EXISTS tb_frete;
DROP TABLE IF EXISTS tb_usuario_empresa;
DROP TABLE IF EXISTS tb_usuario_pcu;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE tb_usuario_empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(14) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE tb_usuario_pcu (
    pcu_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    dt_nasc DATE NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    cnh VARCHAR(20) NOT NULL,
    tipo_veiculo VARCHAR(50) NOT NULL,
    placa VARCHAR(10) NOT NULL,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE tb_frete (
    id_frete INT AUTO_INCREMENT PRIMARY KEY,
    origem VARCHAR(100) NOT NULL,
    uf_origem VARCHAR(2) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    uf_destino VARCHAR(2) NOT NULL,
    peso DECIMAL(10,2) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data_coleta DATE NOT NULL,
    hora_coleta TIME NOT NULL,
    descricao TEXT,
    empresa_id INT NOT NULL,
    carro_necessario VARCHAR(50) NOT NULL,
    status ENUM(
        'Disponivel',
        'Aceito',
        'Em andamento',
        'Aguardando confirmacao',
        'Concluido'
    ) NOT NULL DEFAULT 'Disponivel',
    pcu_id INT NULL,
    data_pagamento DATETIME NULL,

    CONSTRAINT fk_frete_empresa
        FOREIGN KEY (empresa_id)
        REFERENCES tb_usuario_empresa(id_empresa),

    CONSTRAINT fk_frete_pcu
        FOREIGN KEY (pcu_id)
        REFERENCES tb_usuario_pcu(pcu_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE tb_carteira (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    pcu_id INT NOT NULL UNIQUE,

    CONSTRAINT fk_carteira_pcu
        FOREIGN KEY (pcu_id)
        REFERENCES tb_usuario_pcu(pcu_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tb_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    frete_id INT NOT NULL,
    empresa_id INT NOT NULL,
    usuario_pcu_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data DATETIME NULL,
    metodo ENUM('PIX', 'Cartao', 'Boleto') NULL,
    status ENUM(
        'Nao liberado',
        'Pendente',
        'Processando',
        'Pago'
    ) NOT NULL DEFAULT 'Nao liberado',

    CONSTRAINT fk_pagamento_frete
        FOREIGN KEY (frete_id)
        REFERENCES tb_frete(id_frete),

    CONSTRAINT fk_pagamento_empresa
        FOREIGN KEY (empresa_id)
        REFERENCES tb_usuario_empresa(id_empresa),

    CONSTRAINT fk_pagamento_pcu
        FOREIGN KEY (usuario_pcu_id)
        REFERENCES tb_usuario_pcu(pcu_id)
) ENGINE=InnoDB;

CREATE TABLE tb_movimentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carteira_id INT NOT NULL,
    frete_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('Entrada', 'Saida') NOT NULL,
    descricao VARCHAR(255),
    data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pagamento_id INT NULL,

    CONSTRAINT fk_movimentacao_carteira
        FOREIGN KEY (carteira_id)
        REFERENCES tb_carteira(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_movimentacao_frete
        FOREIGN KEY (frete_id)
        REFERENCES tb_frete(id_frete),

    CONSTRAINT fk_movimentacao_pagamento
        FOREIGN KEY (pagamento_id)
        REFERENCES tb_pagamento(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;
