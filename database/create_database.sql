-- =====================================================================
-- SISTEMA DE SEGUIMIENTO DE PACIENTES CRÓNICOS
-- Clínica Médica Aguachica
-- Script de creación de base de datos
-- =====================================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS seguimiento_pacientes 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE seguimiento_pacientes;

-- =====================================================================
-- TABLA: roles
-- =====================================================================
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    es_activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: users (usuarios del sistema)
-- =====================================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    especialidad VARCHAR(150),
    registro_medico VARCHAR(50),
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    es_activo BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: tipos_enfermedades
-- =====================================================================
CREATE TABLE tipos_enfermedades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    codigo_cie10 VARCHAR(10),
    parametros_control JSON,
    es_activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: pacientes
-- =====================================================================
CREATE TABLE pacientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_identidad VARCHAR(20) NOT NULL UNIQUE,
    tipo_documento VARCHAR(10) DEFAULT 'CC',
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero ENUM('M', 'F', 'Otro') NOT NULL,
    telefono VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(150),
    direccion TEXT,
    ciudad VARCHAR(100),
    departamento VARCHAR(100),
    eps VARCHAR(150),
    ips_primaria VARCHAR(150),
    estado ENUM('Activo', 'Inactivo', 'Fallecido') DEFAULT 'Activo',
    observaciones TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_paciente_apellidos (apellidos),
    INDEX idx_paciente_estado (estado)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: paciente_enfermedades (relación N:N)
-- =====================================================================
CREATE TABLE paciente_enfermedades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id BIGINT UNSIGNED NOT NULL,
    tipo_enfermedad_id BIGINT UNSIGNED NOT NULL,
    fecha_diagnostico DATE NOT NULL,
    estado ENUM('Activo', 'En Remisión', 'Controlado') DEFAULT 'Activo',
    observaciones TEXT,
    es_principal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_pe_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_pe_enfermedad FOREIGN KEY (tipo_enfermedad_id) REFERENCES tipos_enfermedades(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_paciente_enfermedad (paciente_id, tipo_enfermedad_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: historias_clinicas
-- =====================================================================
CREATE TABLE historias_clinicas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id BIGINT UNSIGNED NOT NULL,
    medico_id BIGINT UNSIGNED NOT NULL,
    fecha_atencion DATE NOT NULL,
    hora_atencion TIME NOT NULL,
    tipo_atencion VARCHAR(50) NOT NULL,
    motivo_consulta TEXT NOT NULL,
    enfermedad_actual TEXT,
    antecedentes TEXT,
    examen_fisico TEXT,
    impresion_diagnostica TEXT NOT NULL,
    plan_manipulacion TEXT,
    indicaciones TEXT,
    estado ENUM('Abierta', 'Cerrada', 'En seguimiento') DEFAULT 'Abierta',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_hc_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_hc_medico FOREIGN KEY (medico_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_hc_paciente_fecha (paciente_id, fecha_atencion),
    INDEX idx_hc_medico (medico_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: signos_vitales
-- =====================================================================
CREATE TABLE signos_vitales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    historia_clinica_id BIGINT UNSIGNED NOT NULL,
    paciente_id BIGINT UNSIGNED NOT NULL,
    presion_sistolica DECIMAL(5,2),
    presion_diastolica DECIMAL(5,2),
    frecuencia_cardiaca DECIMAL(5,2),
    frecuencia_respiratoria DECIMAL(5,2),
    temperatura DECIMAL(5,2),
    peso DECIMAL(6,2),
    talla DECIMAL(5,2),
    imc DECIMAL(5,2),
    glucosa DECIMAL(6,2),
    saturacion_oxigeno DECIMAL(5,2),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_sv_hc FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE CASCADE,
    CONSTRAINT fk_sv_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    INDEX idx_sv_paciente_fecha (paciente_id, created_at)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: citas
-- =====================================================================
CREATE TABLE citas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id BIGINT UNSIGNED NOT NULL,
    medico_id BIGINT UNSIGNED NOT NULL,
    fecha_cita DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME,
    tipo_cita VARCHAR(50) NOT NULL,
    consultorio VARCHAR(20),
    estado ENUM('Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No Presentado') DEFAULT 'Programada',
    motivo TEXT,
    observaciones TEXT,
    recordatorio_enviado BOOLEAN DEFAULT FALSE,
    fecha_recordatorio TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_cita_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_cita_medico FOREIGN KEY (medico_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_cita_fecha_estado (fecha_cita, estado),
    INDEX idx_cita_medico (medico_id),
    INDEX idx_cita_paciente (paciente_id)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: medicamentos
-- =====================================================================
CREATE TABLE medicamentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    principio_activo VARCHAR(200),
    forma_farmaceutica VARCHAR(50) NOT NULL,
    concentracion VARCHAR(50),
    laboratorio VARCHAR(150),
    es_controlado BOOLEAN DEFAULT FALSE,
    es_activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_medicamento_nombre (nombre)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: prescripciones
-- =====================================================================
CREATE TABLE prescripciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    historia_clinica_id BIGINT UNSIGNED NOT NULL,
    paciente_id BIGINT UNSIGNED NOT NULL,
    medicamento_id BIGINT UNSIGNED NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    frecuencia VARCHAR(50) NOT NULL,
    via_administracion VARCHAR(50) NOT NULL,
    duracion_dias INT,
    indicaciones_especiales TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    es_activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_presc_hc FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE CASCADE,
    CONSTRAINT fk_presc_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_presc_medicamento FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE RESTRICT,
    INDEX idx_presc_paciente_activo (paciente_id, es_activo)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: examenes
-- =====================================================================
CREATE TABLE examenes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    historia_clinica_id BIGINT UNSIGNED NOT NULL,
    paciente_id BIGINT UNSIGNED NOT NULL,
    tipo_examen VARCHAR(100) NOT NULL,
    nombre_examen VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha_solicitud DATE NOT NULL,
    fecha_realizacion DATE,
    laboratorio VARCHAR(200),
    estado ENUM('Solicitado', 'En Proceso', 'Completado', 'Cancelado') DEFAULT 'Solicitado',
    resultados TEXT,
    observaciones_resultado TEXT,
    archivo_resultado VARCHAR(500),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_exam_hc FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE CASCADE,
    CONSTRAINT fk_exam_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    INDEX idx_exam_paciente_estado (paciente_id, estado),
    INDEX idx_exam_fecha (fecha_solicitud)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: configuracion_alertas
-- =====================================================================
CREATE TABLE configuracion_alertas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_enfermedad_id BIGINT UNSIGNED NOT NULL,
    nombre_parametro VARCHAR(100) NOT NULL,
    valor_minimo DECIMAL(10,2),
    valor_maximo DECIMAL(10,2),
    unidad VARCHAR(20) NOT NULL,
    nivel_alerta ENUM('Bajo', 'Medio', 'Alto') DEFAULT 'Medio',
    mensaje_alerta TEXT,
    es_activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_config_enfermedad FOREIGN KEY (tipo_enfermedad_id) REFERENCES tipos_enfermedades(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enfermedad_parametro (tipo_enfermedad_id, nombre_parametro)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: alertas
-- =====================================================================
CREATE TABLE alertas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id BIGINT UNSIGNED NOT NULL,
    signo_vital_id BIGINT UNSIGNED NULL,
    historia_clinica_id BIGINT UNSIGNED NULL,
    tipo_alerta VARCHAR(50) NOT NULL,
    parametro VARCHAR(100) NOT NULL,
    valor_detectado DECIMAL(10,2),
    valor_referencia_min DECIMAL(10,2),
    valor_referencia_max DECIMAL(10,2),
    nivel_severidad ENUM('Info', 'Advertencia', 'Crítico') DEFAULT 'Advertencia',
    mensaje TEXT NOT NULL,
    es_leida BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    usuario_lectura_id BIGINT UNSIGNED NULL,
    observaciones_atencion TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_alerta_paciente FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_alerta_sv FOREIGN KEY (signo_vital_id) REFERENCES signos_vitales(id) ON DELETE SET NULL,
    CONSTRAINT fk_alerta_hc FOREIGN KEY (historia_clinica_id) REFERENCES historias_clinicas(id) ON DELETE SET NULL,
    CONSTRAINT fk_alerta_usuario FOREIGN KEY (usuario_lectura_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_alerta_paciente_leida (paciente_id, es_leida),
    INDEX idx_alerta_severidad (nivel_severidad),
    INDEX idx_alerta_fecha (created_at)
) ENGINE=InnoDB;

-- =====================================================================
-- TABLA: audit_logs
-- =====================================================================
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    accion VARCHAR(50) NOT NULL,
    tabla_afectada VARCHAR(100) NOT NULL,
    registro_id BIGINT UNSIGNED,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_audit_tabla_registro (tabla_afectada, registro_id),
    INDEX idx_audit_usuario (user_id),
    INDEX idx_audit_fecha (created_at)
) ENGINE=InnoDB;

-- =====================================================================
-- DATOS INICIALES (SEEDS)
-- =====================================================================

-- Insertar roles
INSERT INTO roles (nombre, descripcion, es_activo, created_at, updated_at) VALUES
('Administrador', 'Acceso total al sistema, gestión de usuarios y configuraciones', TRUE, NOW(), NOW()),
('Médico', 'Registro de historias clínicas, prescripciones y seguimiento de pacientes', TRUE, NOW(), NOW()),
('Enfermero', 'Registro de signos vitales, aplicación de medicamentos y apoyo en consultas', TRUE, NOW(), NOW()),
('Administrativo', 'Gestión de citas, facturación y reportes administrativos', TRUE, NOW(), NOW());

-- Insertar tipos de enfermedades
INSERT INTO tipos_enfermedades (nombre, descripcion, codigo_cie10, parametros_control, es_activo, created_at, updated_at) VALUES
('Diabetes Mellitus Tipo 2', 'Enfermedad crónica que afecta la forma en que el cuerpo procesa la glucosa', 'E11', '{"glucosa": {"min": 70, "max": 180, "unidad": "mg/dL"}, "peso": {"min": 40, "max": 150, "unidad": "kg"}}', TRUE, NOW(), NOW()),
('Hipertensión Arterial', 'Condición crónica caracterizada por presión arterial elevada de forma sostenida', 'I10', '{"presion_sistolica": {"min": 90, "max": 140, "unidad": "mmHg"}, "presion_diastolica": {"min": 60, "max": 90, "unidad": "mmHg"}}', TRUE, NOW(), NOW()),
('Enfermedad Cardiovascular', 'Grupo de trastornos que afectan el corazón y los vasos sanguíneos', 'I25', '{"presion_sistolica": {"min": 90, "max": 130, "unidad": "mmHg"}, "frecuencia_cardiaca": {"min": 60, "max": 100, "unidad": "lpm"}}', TRUE, NOW(), NOW()),
('Enfermedad Renal Crónica', 'Pérdida progresiva e irreversible de la función renal', 'N18', '{"presion_sistolica": {"min": 90, "max": 130, "unidad": "mmHg"}}', TRUE, NOW(), NOW()),
('EPOC', 'Enfermedad pulmonar obstructiva crónica que dificulta la respiración', 'J44', '{"saturacion_oxigeno": {"min": 88, "max": 100, "unidad": "%"}}', TRUE, NOW(), NOW());

-- Insertar usuarios (contraseña: password123)
INSERT INTO users (role_id, nombre, apellidos, email, telefono, especialidad, registro_medico, password, es_activo, created_at, updated_at) VALUES
(1, 'Administrador', 'Sistema', 'admin@clinicaaguachica.com', '3001234567', NULL, NULL, '$2y$12$LJ3m4ys3Gvv1C5xQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQ', TRUE, NOW(), NOW()),
(2, 'Carlos', 'García Martínez', 'cgarcia@clinicaaguachica.com', '3002345678', 'Medicina Interna', 'MED-12345', '$2y$12$LJ3m4ys3Gvv1C5xQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQ', TRUE, NOW(), NOW()),
(2, 'María', 'López Hernández', 'mlopez@clinicaaguachica.com', '3003456789', 'Endocrinología', 'MED-23456', '$2y$12$LJ3m4ys3Gvv1C5xQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQ', TRUE, NOW(), NOW()),
(3, 'Ana', 'Rodríguez Silva', 'arodriguez@clinicaaguachica.com', '3004567890', NULL, NULL, '$2y$12$LJ3m4ys3Gvv1C5xQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQ', TRUE, NOW(), NOW()),
(4, 'Pedro', 'Sánchez Gómez', 'psanchez@clinicaaguachica.com', '3005678901', NULL, NULL, '$2y$12$LJ3m4ys3Gvv1C5xQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQxQ', TRUE, NOW(), NOW());

-- Insertar medicamentos
INSERT INTO medicamentos (nombre, principio_activo, forma_farmaceutica, concentracion, laboratorio, es_controlado, es_activo, created_at, updated_at) VALUES
('Metformina', 'Metformina hidrocloruro', 'Tableta', '850mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Glimepirida', 'Glimepirida', 'Tableta', '2mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Insulina Glargina', 'Insulina glargina', 'Solución inyectable', '100 UI/mL', 'Lantus', TRUE, TRUE, NOW(), NOW()),
('Losartán', 'Losartán potásico', 'Tableta', '50mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Enalapril', 'Enalapril maleato', 'Tableta', '20mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Amlodipino', 'Besilato de amlodipino', 'Tableta', '5mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Hidroclorotiazida', 'Hidroclorotiazida', 'Tableta', '25mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Atorvastatina', 'Atorvastatina cálcica', 'Tableta', '20mg', 'Genérico', FALSE, TRUE, NOW(), NOW()),
('Ácido acetilsalicílico', 'Ácido acetilsalicílico', 'Tableta', '100mg', 'Genérico', FALSE, TRUE, NOW(), NOW());

-- Insertar configuración de alertas para Diabetes
INSERT INTO configuracion_alertas (tipo_enfermedad_id, nombre_parametro, valor_minimo, valor_maximo, unidad, nivel_alerta, mensaje_alerta, es_activo, created_at, updated_at) VALUES
(1, 'glucosa', 70, 180, 'mg/dL', 'Medio', 'Glucosa fuera de rango normal para paciente diabético', TRUE, NOW(), NOW()),
(1, 'glucosa', 54, 300, 'mg/dL', 'Alto', 'VALOR CRÍTICO: Glucosa en rango de emergencia', TRUE, NOW(), NOW()),
(1, 'presion_sistolica', 90, 140, 'mmHg', 'Medio', 'Presión sistólica fuera de rango para diabético', TRUE, NOW(), NOW());

-- Insertar configuración de alertas para Hipertensión
INSERT INTO configuracion_alertas (tipo_enfermedad_id, nombre_parametro, valor_minimo, valor_maximo, unidad, nivel_alerta, mensaje_alerta, es_activo, created_at, updated_at) VALUES
(2, 'presion_sistolica', 90, 140, 'mmHg', 'Medio', 'Presión sistólica elevada', TRUE, NOW(), NOW()),
(2, 'presion_sistolica', 90, 180, 'mmHg', 'Alto', 'CRISIS HIPERTENSIVA: Presión sistólica peligrosamente alta', TRUE, NOW(), NOW()),
(2, 'presion_diastolica', 60, 90, 'mmHg', 'Medio', 'Presión diastólica elevada', TRUE, NOW(), NOW()),
(2, 'presion_diastolica', 60, 120, 'mmHg', 'Alto', 'CRISIS HIPERTENSIVA: Presión diastólica peligrosamente alta', TRUE, NOW(), NOW());

-- Insertar configuración de alertas para Enfermedad Cardiovascular
INSERT INTO configuracion_alertas (tipo_enfermedad_id, nombre_parametro, valor_minimo, valor_maximo, unidad, nivel_alerta, mensaje_alerta, es_activo, created_at, updated_at) VALUES
(3, 'frecuencia_cardiaca', 60, 100, 'lpm', 'Medio', 'Frecuencia cardíaca fuera de rango normal', TRUE, NOW(), NOW()),
(3, 'presion_sistolica', 90, 130, 'mmHg', 'Alto', 'Presión sistólica crítica para paciente cardiovascular', TRUE, NOW(), NOW());

-- =====================================================================
-- VISTAS ÚTILES
-- =====================================================================

-- Vista de pacientes con sus enfermedades
CREATE VIEW vw_pacientes_enfermedades AS
SELECT 
    p.id,
    p.documento_identidad,
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
    p.fecha_nacimiento,
    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
    p.genero,
    p.estado,
    GROUP_CONCAT(DISTINCT te.nombre SEPARATOR ', ') AS enfermedades,
    COUNT(DISTINCT te.id) AS total_enfermedades
FROM pacientes p
LEFT JOIN paciente_enfermedades pe ON p.id = pe.paciente_id
LEFT JOIN tipos_enfermedades te ON pe.tipo_enfermedad_id = te.id
WHERE p.deleted_at IS NULL
GROUP BY p.id;

-- Vista de alertas pendientes
CREATE VIEW vw_alertas_pendientes AS
SELECT 
    a.id,
    CONCAT(p.nombres, ' ', p.apellidos) AS paciente,
    p.documento_identidad,
    a.tipo_alerta,
    a.parametro,
    a.valor_detectado,
    a.nivel_severidad,
    a.mensaje,
    a.created_at AS fecha_alerta
FROM alertas a
JOIN pacientes p ON a.paciente_id = p.id
WHERE a.es_leida = FALSE
ORDER BY 
    CASE a.nivel_severidad
        WHEN 'Crítico' THEN 1
        WHEN 'Advertencia' THEN 2
        WHEN 'Info' THEN 3
    END,
    a.created_at DESC;

-- Vista de citas de hoy
CREATE VIEW vw_citas_hoy AS
SELECT 
    c.id,
    c.fecha_cita,
    c.hora_inicio,
    c.tipo_cita,
    c.estado,
    CONCAT(p.nombres, ' ', p.apellidos) AS paciente,
    CONCAT(u.nombre, ' ', u.apellidos) AS medico,
    c.consultorio
FROM citas c
JOIN pacientes p ON c.paciente_id = p.id
JOIN users u ON c.medico_id = u.id
WHERE c.fecha_cita = CURDATE()
    AND c.deleted_at IS NULL
ORDER BY c.hora_inicio;

-- =====================================================================
-- STORED PROCEDURES
-- =====================================================================

DELIMITER //

-- Procedimiento para registrar signos vitales y generar alertas
CREATE PROCEDURE sp_registrar_signos_vitales(
    IN p_historia_clinica_id BIGINT,
    IN p_paciente_id BIGINT,
    IN p_presion_sistolica DECIMAL(5,2),
    IN p_presion_diastolica DECIMAL(5,2),
    IN p_frecuencia_cardiaca DECIMAL(5,2),
    IN p_temperatura DECIMAL(5,2),
    IN p_peso DECIMAL(6,2),
    IN p_talla DECIMAL(5,2),
    IN p_glucosa DECIMAL(6,2),
    IN p_saturacion_oxigeno DECIMAL(5,2)
)
BEGIN
    DECLARE v_imc DECIMAL(5,2);
    DECLARE v_alerta_mensaje TEXT;
    DECLARE v_nivel_alerta VARCHAR(20);
    
    -- Calcular IMC
    IF p_peso IS NOT NULL AND p_talla IS NOT NULL THEN
        SET v_imc = p_peso / POWER(p_talla / 100, 2);
    END IF;
    
    -- Insertar signos vitales
    INSERT INTO signos_vitales (
        historia_clinica_id, paciente_id, presion_sistolica, presion_diastolica,
        frecuencia_cardiaca, temperatura, peso, talla, imc, glucosa, saturacion_oxigeno,
        created_at, updated_at
    ) VALUES (
        p_historia_clinica_id, p_paciente_id, p_presion_sistolica, p_presion_diastolica,
        p_frecuencia_cardiaca, p_temperatura, p_peso, p_talla, v_imc, p_glucosa, p_saturacion_oxigeno,
        NOW(), NOW()
    );
    
    -- Verificar alertas para glucosa
    IF p_glucosa IS NOT NULL THEN
        SELECT 
            CASE 
                WHEN p_glucosa < 54 OR p_glucosa > 300 THEN 'Crítico'
                WHEN p_glucosa < 70 OR p_glucosa > 180 THEN 'Advertencia'
                ELSE NULL
            END,
            CASE 
                WHEN p_glucosa < 54 THEN CONCAT('Hipoglucemia severa: ', p_glucosa, ' mg/dL')
                WHEN p_glucosa > 300 THEN CONCAT('Hiperglucemia severa: ', p_glucosa, ' mg/dL')
                WHEN p_glucosa < 70 THEN CONCAT('Hipoglucemia: ', p_glucosa, ' mg/dL')
                WHEN p_glucosa > 180 THEN CONCAT('Hiperglucemia: ', p_glucosa, ' mg/dL')
                ELSE NULL
            END
        INTO v_nivel_alerta, v_alerta_mensaje;
        
        IF v_nivel_alerta IS NOT NULL THEN
            INSERT INTO alertas (
                paciente_id, tipo_alerta, parametro, valor_detectado,
                valor_referencia_min, valor_referencia_max, nivel_severidad, mensaje,
                created_at, updated_at
            ) VALUES (
                p_paciente_id, 'Valor crítico', 'glucosa', p_glucosa,
                70, 180, v_nivel_alerta, v_alerta_mensaje, NOW(), NOW()
            );
        END IF;
    END IF;
    
    -- Verificar alertas para presión arterial
    IF p_presion_sistolica IS NOT NULL THEN
        SELECT 
            CASE 
                WHEN p_presion_sistolica > 180 THEN 'Crítico'
                WHEN p_presion_sistolica > 140 THEN 'Advertencia'
                ELSE NULL
            END,
            CASE 
                WHEN p_presion_sistolica > 180 THEN CONCAT('CRISIS HIPERTENSIVA: Sistólica ', p_presion_sistolica, ' mmHg')
                WHEN p_presion_sistolica > 140 THEN CONCAT('Presión sistólica elevada: ', p_presion_sistolica, ' mmHg')
                ELSE NULL
            END
        INTO v_nivel_alerta, v_alerta_mensaje;
        
        IF v_nivel_alerta IS NOT NULL THEN
            INSERT INTO alertas (
                paciente_id, tipo_alerta, parametro, valor_detectado,
                valor_referencia_min, valor_referencia_max, nivel_severidad, mensaje,
                created_at, updated_at
            ) VALUES (
                p_paciente_id, 'Valor crítico', 'presion_sistolica', p_presion_sistolica,
                90, 140, v_nivel_alerta, v_alerta_mensaje, NOW(), NOW()
            );
        END IF;
    END IF;
    
    SELECT LAST_INSERT_ID() AS signo_vital_id;
END //

DELIMITER ;

-- =====================================================================
-- CONSULTAS DE EJEMPLO
-- =====================================================================

-- 1. Obtener pacientes con Diabetes activa
-- SELECT * FROM vw_pacientes_enfermedades WHERE enfermedades LIKE '%Diabetes%';

-- 2. Ver alertas críticas pendientes
-- SELECT * FROM vw_alertas_pendientes WHERE nivel_severidad = 'Crítico';

-- 3. Historial de signos vitales de un paciente
-- SELECT * FROM signos_vitales WHERE paciente_id = 1 ORDER BY created_at DESC;

-- 4. Próximas citas de un médico
-- SELECT * FROM citas WHERE medico_id = 2 AND fecha_cita >= CURDATE() ORDER BY fecha_cita, hora_inicio;

-- 5. Pacientes con más de 2 enfermedades crónicas
-- SELECT * FROM vw_pacientes_enfermedades WHERE total_enfermedades > 2;
