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

CREATE TABLE pedidos(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_cliente BIGINT UNSIGNED NOT NULL,
    fechaPedido DATE NOT NULL,
    estado VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE detalle_pedidos(
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pedido BIGINT UNSIGNED NOT NULL,
    id_producto BIGINT UNSIGNED NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    foreign key (id_pedido) references pedidos(id),
    foreign key (id_producto) references productos(id)
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
DESC pedidos;
DESC detalle_pedidos;




