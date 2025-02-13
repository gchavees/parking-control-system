CREATE DATABASE estacionamento;
USE estacionamento;

CREATE TABLE veiculos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    placa VARCHAR(10) NOT NULL UNIQUE,
    data_entrada DATETIME NOT NULL,
    data_saida DATETIME,
    valor DECIMAL(10,2)
);

-- Valor por hora (pode ser ajustado)
CREATE TABLE precos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    valor_hora DECIMAL(10,2) NOT NULL
);

INSERT INTO precos (valor_hora) VALUES (10.00);