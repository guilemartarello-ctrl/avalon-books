CREATE DATABASE IF NOT EXISTS avalon_books
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE avalon_books;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255) DEFAULT 'assets/img/sem-capa.png',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    forma_pagamento ENUM('PIX', 'Boleto', 'Cartao') NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'Pago',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

INSERT INTO categorias (nome) VALUES
('Ficção'),
('Tecnologia'),
('Fantasia'),
('Mangás e HQ');

INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque, imagem) VALUES
(1, '1984 - George Orwell', 'Clássico distópico sobre vigilância e controle.', 39.90, 15, 'https://placehold.co/300x400?text=1984'),
(2, 'Código Limpo', 'Habilidades práticas do Agile Software por Robert C. Martin.', 89.90, 8, 'https://placehold.co/300x400?text=Codigo+Limpo'),
(3, 'O Hobbit', 'A jornada de Bilbo Bolseiro pela Terra-média.', 54.50, 12, 'https://placehold.co/300x400?text=O+Hobbit'),
(4, 'Fullmetal Alchemist Vol. 1', 'A história dos irmãos Elric em busca da Pedra Filosofal.', 34.90, 20, 'https://placehold.co/300x400?text=FMA+Vol+1');