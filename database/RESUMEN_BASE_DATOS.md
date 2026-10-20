# 📊 Base de Datos - Sistema de Seguimiento de Pacientes Crónicos

## ✅ Resumen de lo creado

He diseñado completamente la base de datos para el sistema de seguimiento de pacientes crónicos de la Clínica Médica Aguachica. Aquí está todo lo que se ha creado:

## 📁 Archivos creados

### Migraciones Laravel (13 archivos)
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php (actualizado)
├── 2024_01_01_000001_create_roles_table.php
├── 2024_01_01_000002_create_tipos_enfermedades_table.php
├── 2024_01_01_000003_create_pacientes_table.php
├── 2024_01_01_000004_create_paciente_enfermedades_table.php
├── 2024_01_01_000005_create_historias_clinicas_table.php
├── 2024_01_01_000006_create_signos_vitales_table.php
├── 2024_01_01_000007_create_citas_table.php
├── 2024_01_01_000008_create_medicamentos_table.php
├── 2024_01_01_000009_create_prescripciones_table.php
├── 2024_01_01_000010_create_examenes_table.php
├── 2024_01_01_000011_create_configuracion_alertas_table.php
├── 2024_01_01_000012_create_alertas_table.php
└── 2024_01_01_000013_create_audit_logs_table.php
```

### Modelos Eloquent (12 archivos)
```
app/Models/
├── Role.php
├── User.php (actualizado)
├── TipoEnfermedad.php
├── Paciente.php
├── PacienteEnfermedad.php
├── HistoriaClinica.php
├── SignoVital.php
├── Cita.php
├── Medicamento.php
├── Prescripcion.php
├── Examen.php
├── ConfiguracionAlerta.php
├── Alerta.php
└── AuditLog.php
```

### Seeders (5 archivos)
```
database/seeders/
├── DatabaseSeeder.php
├── RolesSeeder.php
├── TiposEnfermedadesSeeder.php
├── UsersSeeder.php
├── MedicamentosSeeder.php
└── ConfiguracionAlertasSeeder.php
```

### Documentación y Script SQL
```
database/
├── DATABASE_DESIGN.md (Diagrama ER completo)
└── create_database.sql (Script SQL directo)
```

## 🗂️ Tablas creadas (14 tablas)

| # | Tabla | Descripción |
|---|-------|-------------|
| 1 | **roles** | Roles del sistema (Admin, Médico, Enfermero, Administrativo) |
| 2 | **users** | Usuarios del sistema con roles |
| 3 | **tipos_enfermedades** | Catálogo de enfermedades crónicas |
| 4 | **pacientes** | Información demográfica de pacientes |
| 5 | **paciente_enfermedades** | Relación N:N pacientes-enfermedades |
| 6 | **historias_clinicas** | Registro de atenciones médicas |
| 7 | **signos_vitales** | Mediciones del paciente |
| 8 | **citas** | Agendamiento de citas |
| 9 | **medicamentos** | Catálogo de medicamentos |
| 10 | **prescripciones** | Tratamientos medicamentosos |
| 11 | **examenes** | Solicitudes y resultados de exámenes |
| 12 | **configuracion_alertas** | Umbrales para alertas automáticas |
| 13 | **alertas** | Alertas generadas por el sistema |
| 14 | **audit_logs** | Registro de auditoría |

## 🔗 Relaciones principales

```
roles (1) ──────→ (N) users
users (1) ──────→ (N) historias_clinicas
users (1) ──────→ (N) citas
pacientes (1) ──→ (N) historias_clinicas
pacientes (1) ──→ (N) citas
pacientes (1) ──→ (N) signos_vitales
pacientes (1) ──→ (N) prescripciones
pacientes (1) ──→ (N) examenes
pacientes (1) ──→ (N) alertas
tipos_enfermedades (1) → (N) configuracion_alertas
historias_clinicas (1) → (N) signos_vitales
historias_clinicas (1) → (N) prescripciones
historias_clinicas (1) → (N) examenes
medicamentos (1) ─────→ (N) prescripciones
```

## 🚀 Cómo usar

### Opción 1: Usando Laravel Migrations (Recomendado)

```bash
# 1. Configurar archivo .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seguimiento_pacientes
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# 2. Crear la base de datos en MySQL
mysql -u root -p -e "CREATE DATABASE seguimiento_pacientes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Ejecutar migraciones
php artisan migrate

# 4. Insertar datos iniciales
php artisan db:seed
```

### Opción 2: Usando el script SQL directo

```bash
# 1. Abrir phpMyAdmin o MySQL Workbench

# 2. Importar el archivo:
# database/create_database.sql

# O ejecutar desde terminal:
mysql -u root -p < database/create_database.sql
```

## 🔑 Datos iniciales (Seeders)

### Usuarios de prueba
| Email | Contraseña | Rol |
|-------|-----------|-----|
| admin@clinicaaguachica.com | password123 | Administrador |
| cgarcia@clinicaaguachica.com | password123 | Médico |
| mlopez@clinicaaguachica.com | password123 | Médico |
| arodriguez@clinicaaguachica.com | password123 | Enfermero |
| psanchez@clinicaaguachica.com | password123 | Administrativo |

### Enfermedades configuradas
- Diabetes Mellitus Tipo 2 (CIE-10: E11)
- Hipertensión Arterial (CIE-10: I10)
- Enfermedad Cardiovascular (CIE-10: I25)
- Enfermedad Renal Crónica (CIE-10: N18)
- EPOC (CIE-10: J44)

### Medicamentos incluidos
- Metformina, Glimepirida, Insulina (Diabetes)
- Losartán, Enalapril, Amlodipino, Hidroclorotiazida (Hipertensión)
- Atorvastatina, Ácido acetilsalicílico (Cardiovascular)

## ⚙️ Características implementadas

### 1. **Soft Deletes**
Todas las tablas principales tienen `deleted_at` para eliminación suave.

### 2. **Auditoría completa**
La tabla `audit_logs` registra:
- Quién hizo la acción
- Qué acción (Create, Read, Update, Delete)
- Qué tabla y registro afectó
- Datos anteriores y nuevos
- IP y user agent

### 3. **Índices optimizados**
- Índices en campos de búsqueda frecuente
- Índices compuestos para consultas comunes
- Foreign keys con proper indexes

### 4. **Alertas automáticas**
- Configuración por tipo de enfermedad
- Umbrales mínimos y máximos
- Niveles de severidad (Info, Advertencia, Crítico)
- Stored procedure para generar alertas automáticamente

### 5. **Vistas útiles**
- `vw_pacientes_enfermedades`: Pacientes con sus enfermedades
- `vw_alertas_pendientes`: Alertas no atendidas
- `vw_citas_hoy`: Citas del día

### 6. **Stored Procedures**
- `sp_registrar_signos_vitales`: Registra signos vitales y genera alertas automáticamente

## 📈 Tamaño estimado de la base de datos

| Concepto | Registros/año | Tamaño |
|----------|--------------|--------|
| Pacientes | 500-1,000 | ~500 KB |
| Historias clínicas | 5,000-10,000 | ~5 MB |
| Signos vitales | 20,000-30,000 | ~10 MB |
| Citas | 10,000-15,000 | ~2 MB |
| Alertas | 5,000-8,000 | ~1 MB |
| **Total anual** | | **~20 MB** |

## 🔒 Seguridad implementada

1. **Cifrado de contraseñas**: Hash bcrypt automático
2. **Foreign keys**: Integridad referencial garantizada
3. **Soft deletes**: No se pierden datos históricos
4. **Audit logs**: Rastro completo de auditoría
5. **JSON para parámetros flexibles**: Campos JSON para configuraciones

## ✅ Próximos pasos

Una vez creada la base de datos, puedes:

1. **Configurar autenticación** con Laravel Sanctum
2. **Crear controladores** para cada módulo
3. **Desarrollar rutas API** para el frontend
4. **Implementar lógica de alertas** automáticas
5. **Crear interfaces** para el registro de datos

## 📝 Notas importantes

- La tabla `users` fue actualizada para incluir `role_id`, `especialidad` y `registro_medico`
- Los medicamentos de control requieren validación especial
- Las alertas se generan automáticamente al registrar signos vitales fuera de rango
- El sistema está diseñado para cumplir con la Ley 1581 de 2012 (protección de datos)

---

**¿Necesitas ayuda con algún módulo específico o con la implementación de alguna funcionalidad?**
