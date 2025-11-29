CREATE DATABASE AlixSport_db;
DROP DATABASE AlixSport;
USE AlixSport_db;

/* 
offset of 4
YoSoyAdmin1987  →  CsWscEhqmr1987
YoSoyUsuario1983  →  CsWscYwyevms1983 
*/
USE AlixSport_db;

CREATE TABLE usuarios (
    id_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_Usuario VARCHAR(100) NOT NULL,
    email_Usuario VARCHAR(150) UNIQUE NOT NULL,
    password_Usuario VARCHAR(255) NOT NULL,
    rol ENUM('CsWscEhqmr1987', 'CsWscYwyevms1983') DEFAULT 'CsWscYwyevms1983' NOT NULL,
    fecha_Registro_Usuario TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

USE AlixSport_db;
CREATE TABLE codigos_recuperacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    fecha_expiracion TIMESTAMP NOT NULL
);

USE AlixSport_db;
CREATE TABLE productos (
    id_Producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_Producto VARCHAR(150) NOT NULL,
    descripcion_Producto TEXT,
    sabor_Producto  VARCHAR(50) NOT NULL,
    precio_Producto DECIMAL(10,2) NOT NULL,
    stock_Producto INT DEFAULT 0,
    imagen_Producto TEXT,
    categoria_Producto VARCHAR(100) NOT NULL,
    fecha_Creacion_Producto  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

USE AlixSport_db;
CREATE TABLE sabores (
    id_Sabor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_Sabor VARCHAR(100) NOT NULL UNIQUE
);

USE AlixSport_db;
CREATE TABLE categorias (
    id_Categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_Categoria VARCHAR(100) NOT NULL UNIQUE
);

USE AlixSport_db;
CREATE TABLE carrito (
    id_Carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_Usuario INT NOT NULL,
    fecha_creacion_Carrito TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT  tb_carrito_tb_usuario_fk FOREIGN KEY (id_Usuario) REFERENCES usuarios(id_Usuario) ON DELETE CASCADE
);

USE AlixSport_db;
CREATE TABLE carrito_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_Carrito INT NOT NULL,
    id_Producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    CONSTRAINT  tb_carrito_detalle_tb_carrito_fk FOREIGN KEY (id_Carrito) REFERENCES carrito(id_Carrito) ON DELETE CASCADE,
    CONSTRAINT  tb_carrito_detalle_tb_productos_fk FOREIGN KEY (id_Producto) REFERENCES productos(id_Producto) ON DELETE CASCADE
);

CREATE TRIGGER before_insert_carrito_detalle
BEFORE INSERT ON carrito_detalle
FOR EACH ROW
BEGIN
    DECLARE v_precio DECIMAL(10,2);

    SELECT precio_Producto INTO v_precio
    FROM productos
    WHERE id_Producto = NEW.id_Producto;

    SET NEW.precio_unitario = v_precio;
END;

USE AlixSport_db;

INSERT INTO usuarios (nombre_Usuario, email_Usuario, password_Usuario, rol) VALUES
('Admin Juan', 'juan.admin@alixsport.com', SHA2('Admin1234', 256), 'CsWscEhqmr1987'),
('Carlos Gómez', 'carlos@alixsport.com', SHA2('User2025', 256), 'CsWscYwyevms1983');

USE AlixSport_db;
INSERT INTO productos (nombre_Producto, descripcion_Producto, sabor_Producto, precio_Producto, stock_Producto, imagen_Producto, categoria_Producto)
VALUES
('Proteína Whey 1kg', 'Proteína concentrada de suero de leche 1kg.', '1,3,4', 120000, 50, 'whey_vainilla.jpg', '1'),
('Proteína Whey 1kg', 'Proteína concentrada de suero de leche 1kg.', '2,3', 120000, 60, 'whey_chocolate.jpg', '1'),
('Creatina 300g', 'Monohidrato de creatina pura.', '4,2', 80000, 40, 'creatina.jpg', '2'),
('BCAA 400g', 'Aminoácidos de cadena ramificada.', '1,7', 90000, 30, 'bcaa_tropical.jpg', '3'),
('Pre Entreno 300g', 'Suplemento pre-entrenamiento.', '8,2,3', 95000, 25, 'pre_sandia.jpg', '4'),
('Glutamina 300g', 'Apoyo a la recuperación muscular.', '8,1', 85000, 35, 'glutamina.jpg', '3'),
('Proteína Vegana 1kg', 'Proteína vegetal sin lactosa.', '8,4', 110000, 20, 'vegana_vainilla.jpg', '2'),
('Omega 3 120caps', 'Ácidos grasos esenciales.', '8,2', 60000, 80, 'omega3.jpg', 'Vitaminas'),
('Multivitamínico 60caps', 'Vitaminas y minerales esenciales.', '8,1', 70000, 70, 'multivitaminico.jpg', '5'),
('Barra Proteica', 'Snack con alto contenido proteico.', '8,3,4', 5000, 200, 'barra_choco.jpg', '4');

USE AlixSport_db;
INSERT INTO carrito (id_Usuario) VALUES
(1), (2);

USE AlixSport_db;
INSERT INTO carrito_detalle (id_Carrito, id_Producto, cantidad, precio_unitario) VALUES
(1, 1, 1, 0),
(1, 3, 2, 0),
(2, 2, 1, 0),
(2, 5, 1, 0),
(1, 4, 1, 0),
(2, 6, 3, 0),
(1, 7, 1, 0),
(2, 8, 2, 0),
(1, 9, 1, 0),
(2, 10, 5, 0);

USE AlixSport_db;
INSERT INTO categorias (nombre_Categoria) VALUES
('Proteína'),
('Creatina'),
('Pre-Entreno'),
('Aminoácidos / BCAA'),
('Ganadores de Masa'),
('Vitaminas y Minerales'),
('Quemadores de Grasa'),
('Recuperadores Musculares');

USE AlixSport_db;
INSERT INTO sabores (nombre_Sabor) VALUES
('Vainilla'),
('Chocolate'),
('Cookies & Cream'),
('Fresa'),
('Banano'),
('Caramelo'),
('Mocha'),
('Natural / Sin Sabor'),
('Uva'),
('Mora Azul (Blueberry)'),
('Sandía'),
('Limón'),
('Naranja');



USE AlixSport_db;
SELECT * FROM productos;  

USE AlixSport_db;
SELECT * FROM codigos_recuperacion