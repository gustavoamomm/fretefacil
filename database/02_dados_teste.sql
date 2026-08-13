-- FreteFácil - Dados de teste
-- Senha dos usuários de teste: 12345678
-- Os dados abaixo são fictícios.

USE fretefacil_db;

INSERT INTO tb_usuario_empresa (nome, cnpj, senha)
VALUES
('Empresa Teste Ltda', '12345678000199', '$2y$12$EQV.o9mjgiCLJPfujBBOVuW9ZLscUhLuCzYt1n5SUNTVxxUe2xMdy');

INSERT INTO tb_usuario_pcu
(nome, dt_nasc, cpf, cnh, tipo_veiculo, placa, senha)
VALUES
('Motorista Teste', '2000-01-15', '12345678901', '12345678900', 'Fiorino', 'ABC1D23', '$2y$12$EQV.o9mjgiCLJPfujBBOVuW9ZLscUhLuCzYt1n5SUNTVxxUe2xMdy');

INSERT INTO tb_carteira (saldo, pcu_id)
SELECT 0.00, pcu_id
FROM tb_usuario_pcu
WHERE cpf = '12345678901';

INSERT INTO tb_frete
(
    origem,
    uf_origem,
    destino,
    uf_destino,
    peso,
    valor,
    data_coleta,
    hora_coleta,
    descricao,
    empresa_id,
    carro_necessario,
    status
)
SELECT
    'Joinville',
    'SC',
    'Curitiba',
    'PR',
    150.00,
    350.00,
    DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY),
    '08:00:00',
    'Frete de teste para demonstração do sistema.',
    id_empresa,
    'Fiorino',
    'Disponivel'
FROM tb_usuario_empresa
WHERE cnpj = '12345678000199';
