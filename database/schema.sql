CREATE DATABASE ECOMMERCE;
USE ECOMMERCE;

CREATE TABLE categorias(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombreCategoria VARCHAR(100) NOT NULL,
    detalleCategoria VARCHAR (100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE productos(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_categoria BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR (100) NOT NULL,
    fechaVencimiento DATE NULL,
    informacion TEXT NOT NULL,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    precio DECIMAL(10,2) NOT NULL,
    unidades INT NOT NULL,
    estado BOOL NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    foreign key (id_categoria) references categorias(id)
    );

INSERT INTO productos (
    id_categoria,
    nombre,
    fechaVencimiento,
    informacion,
    codigo,
    precio,
    unidades,
    estado
)
VALUES
(
    1,
    'Teclado Logitech',
    '2027-12-31',
    'Teclado mecanico',
    'TEC001',
    180000,
    10,
    1
),
(
    1,
    'Mouse Razer',
    '2027-06-12',
    'Mouse gamer',
    'MOU0001',
    30000,
    20,
    1
);

SELECT * FROM categorias;

SELECT * FROM productos;

SHOW CREATE TABLE categorias;
DESC productos;




