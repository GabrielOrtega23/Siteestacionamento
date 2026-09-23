CREATE DATABASE IF NOT EXISTS estacionamento
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE estacionamento;

CREATE TABLE IF NOT EXISTS vagas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_vaga VARCHAR(10) NOT NULL UNIQUE,
    tipo ENUM('Carro', 'Moto', 'Caminhão') NOT NULL DEFAULT 'Carro',
    status ENUM('Livre', 'Ocupada') NOT NULL DEFAULT 'Livre',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tarifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(100) NOT NULL,
    valor_primeira_hora DECIMAL(10,2) NOT NULL DEFAULT 5.00,
    valor_hora_adicional DECIMAL(10,2) NOT NULL DEFAULT 3.00,
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO tarifas (descricao, valor_primeira_hora, valor_hora_adicional, ativo)
VALUES ('Tarifa Padrão', 5.00, 3.00, 1);

CREATE TABLE IF NOT EXISTS registros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL,
    modelo VARCHAR(60) DEFAULT NULL,
    cor VARCHAR(30) DEFAULT NULL,
    vaga_id INT NOT NULL,
    data_entrada DATETIME NOT NULL,
    data_saida DATETIME DEFAULT NULL,
    tempo_permanencia VARCHAR(30) DEFAULT NULL,
    valor_total DECIMAL(10,2) DEFAULT NULL,
    status ENUM('Ativo', 'Finalizado') NOT NULL DEFAULT 'Ativo',
    FOREIGN KEY (vaga_id) REFERENCES vagas(id)
) ENGINE=InnoDB;

CREATE INDEX idx_registros_placa ON registros(placa);
CREATE INDEX idx_registros_status ON registros(status);

INSERT INTO vagas (numero_vaga, tipo, status) VALUES
('A01', 'Carro', 'Livre'),
('A02', 'Carro', 'Livre'),
('A03', 'Carro', 'Livre'),
('A04', 'Carro', 'Livre'),
('A05', 'Carro', 'Livre'),
('M01', 'Moto', 'Livre'),
('M02', 'Moto', 'Livre'),
('C01', 'Caminhão', 'Livre');
