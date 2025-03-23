create database POI;
Use POI;

select * from Usuarios;

-- Tabla de Usuarios
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    fecha_registro DATE NOT NULL,
    foto_perfil longtext NULL,
    tareas_completadas INT DEFAULT 0,
    marco_perfil VARCHAR(100) DEFAULT NULL
);

ALTER TABLE Usuarios ADD COLUMN tareas_completadas INT DEFAULT 0;
ALTER TABLE Usuarios ADD COLUMN marco_perfil VARCHAR(100) DEFAULT NULL;

select * from Chats;
select * from Mensajes;

-- Tabla de Chats
CREATE TABLE Chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario1_id INT NOT NULL,
    usuario2_id INT NOT NULL,
    FOREIGN KEY (usuario1_id) REFERENCES Usuarios(id),
    FOREIGN KEY (usuario2_id) REFERENCES Usuarios(id)
);

select * from Grupos;
select * from Usuarios_Grupos;
-- Tabla de Grupos
CREATE TABLE Grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATE NOT NULL
);

CREATE TABLE Usuarios_Grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    grupo_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (grupo_id) REFERENCES Grupos(id)
);

-- Tabla de Mensajes
CREATE TABLE Mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NULL,
    usuario_id INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_envio DATETIME NOT NULL,
    grupo_id INT NULL,
    FOREIGN KEY (chat_id) REFERENCES Chats(id),
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (grupo_id) REFERENCES Grupos(id)
);

ALTER TABLE Mensajes MODIFY chat_id INT NULL;

ALTER TABLE Mensajes ADD COLUMN grupo_id INT NULL;
ALTER TABLE Mensajes ADD FOREIGN KEY (grupo_id) REFERENCES Grupos(id);


select * from Tareas;
-- Tabla de Tareas
CREATE TABLE Tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_limite DATE NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    grupo_id INT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (grupo_id) REFERENCES Grupos(id)
);

ALTER TABLE Tareas ADD COLUMN grupo_id INT NULL;
ALTER TABLE Tareas ADD FOREIGN KEY (grupo_id) REFERENCES Grupos(id);

-- Tabla de Recompensas
select * from Recompensas;
CREATE TABLE Recompensas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tareas_requeridas INT NOT NULL
);

INSERT INTO Recompensas (nombre, descripcion, tareas_requeridas) VALUES
('Marco Dorado', 'Un marco dorado para tu foto de perfil', 15),
('Marco Plateado', 'Un marco plateado para tu foto de perfil', 10),
('Marco Bronce', 'Un marco bronce para tu foto de perfil', 5);


select * from Recompensas_Reclamadas;
CREATE TABLE Recompensas_Reclamadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    recompensa_id INT NOT NULL,
    fecha_reclamada DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (recompensa_id) REFERENCES Recompensas(id)
);

