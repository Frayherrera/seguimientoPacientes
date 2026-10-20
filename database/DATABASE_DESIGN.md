# Diseño de Base de Datos - Sistema de Seguimiento de Pacientes Crónicos

## 📊 Diagrama Entidad-Relación (ER)

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│      roles      │     │   usuarios      │     │  tipos_         │
├─────────────────┤     ├─────────────────┤     │  enfermedades   │
│ id (PK)         │◄────│ id (PK)         │     ├─────────────────┤
│ nombre          │     │ role_id (FK)    │     │ id (PK)         │
│ descripcion     │     │ nombre          │     │ nombre          │
│ es_activo       │     │ apellidos       │     │ descripcion     │
└─────────────────┘     │ email           │     │ codigo_cie10    │
                        │ especialidad    │     │ parametros_     │
                        │ registro_medico │     │   control       │
                        │ es_activo       │     │ es_activo       │
                        └────────┬────────┘     └────────┬────────┘
                                 │                       │
                                 │                       │
                        ┌────────▼───────────────────────▼────────┐
                        │                                        │
                        │         paciente_enfermedades           │
                        │         (Tabla pivote)                 │
                        ├────────────────────────────────────────┤
                        │ id (PK)                                │
                        │ paciente_id (FK)                       │
                        │ tipo_enfermedad_id (FK)                │
                        │ fecha_diagnostico                      │
                        │ estado                                 │
                        │ es_principal                           │
                        └────────────────────┬───────────────────┘
                                             │
┌─────────────────┐                          │
│   pacientes     │                          │
├─────────────────┤                          │
│ id (PK)         │◄─────────────────────────┘
│ documento_ident │
│ tipo_documento  │
│ nombres         │
│ apellidos       │
│ fecha_nacimiento│
│ genero          │
│ telefono        │
│ celular         │
│ email           │
│ direccion       │
│ eps             │
│ estado          │
└────────┬────────┘
         │
         ├──────────────────────────────────────────────┐
         │                                              │
         ▼                                              ▼
┌─────────────────┐                          ┌─────────────────┐
│historias_       │                          │    citas        │
│clinicas         │                          ├─────────────────┤
├─────────────────┤                          │ id (PK)         │
│ id (PK)         │                          │ paciente_id (FK)│
│ paciente_id (FK)│                          │ medico_id (FK)  │
│ medico_id (FK)  │                          │ fecha_cita      │
│ fecha_atencion  │                          │ hora_inicio     │
│ hora_atencion   │                          │ tipo_cita       │
│ tipo_atencion   │                          │ estado          │
│ motivo_consulta │                          │ recordatorio    │
│ impresion_      │                          └─────────────────┘
│   diagnostica   │
│ plan_           │
│   manipulacion  │
│ estado          │
└────────┬────────┘
         │
         ├──────────────────────┬─────────────────────┐
         │                      │                     │
         ▼                      ▼                     ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│ signos_vitales  │  │ prescripciones  │  │   examenes      │
├─────────────────┤  ├─────────────────┤  ├─────────────────┤
│ id (PK)         │  │ id (PK)         │  │ id (PK)         │
│ historia_       │  │ historia_       │  │ historia_       │
│   clinica_id    │  │   clinica_id    │  │   clinica_id    │
│ paciente_id (FK)│  │ paciente_id (FK)│  │ paciente_id (FK)│
│ presion_        │  │ medicamento_id  │  │ tipo_examen     │
│   sistolica     │  │ dosis           │  │ nombre_examen   │
│ presion_        │  │ frecuencia      │  │ fecha_solicitud │
│   diastolica    │  │ via_admin       │  │ fecha_realiz.   │
│ frecuencia_     │  │ duracion_dias   │  │ resultados      │
│   cardiaca      │  │ fecha_inicio    │  │ estado          │
│ temperatura     │  │ es_activo       │  └─────────────────┘
│ peso            │  └─────────────────┘
│ talla           │
│ imc             │  ┌─────────────────┐
│ glucosa         │  │  medicamentos   │
│ saturacion_ox   │  ├─────────────────┤
└─────────────────┘  │ id (PK)         │
                     │ nombre          │
                     │ principio_activo│
                     │ forma_farma.    │
                     │ concentracion   │
                     └─────────────────┘

┌─────────────────┐        ┌─────────────────┐
│ configuracion_  │        │    alertas      │
│ alertas         │        ├─────────────────┤
├─────────────────┤        │ id (PK)         │
│ id (PK)         │        │ paciente_id (FK)│
│ tipo_enfermedad │        │ signo_vital_id  │
│   _id (FK)      │        │ tipo_alerta     │
│ nombre_parametro│        │ parametro       │
│ valor_minimo    │        │ nivel_severidad │
│ valor_maximo    │        │ mensaje         │
│ unidad          │        │ es_leida        │
│ nivel_alerta    │        └─────────────────┘
│ mensaje_alerta  │
└─────────────────┘        ┌─────────────────┐
                           │   audit_logs    │
                           ├─────────────────┤
                           │ id (PK)         │
                           │ user_id (FK)    │
                           │ accion          │
                           │ tabla_afectada  │
                           │ registro_id     │
                           │ datos_anteriores│
                           │ datos_nuevos    │
                           │ ip_address      │
                           └─────────────────┘
```

## 📋 Descripción de Tablas

### 1. **roles**
Almacena los roles del sistema (médico, enfermero, administrativo).
- `nombre`: Identificador único del rol
- `es_activo`: Permite deshabilitar roles sin eliminarlos

### 2. **usuarios**
Personal de la clínica que accede al sistema.
- `role_id`: Relación con tabla roles
- `especialidad`: Solo para médicos
- `registro_medico`: Número de registro profesional

### 3. **tipos_enfermedades**
Catálogo de enfermedades crónicas que maneja la clínica.
- `codigo_cie10`: Código internacional de clasificación
- `parametros_control`: JSON con parámetros a monitorear por tipo de enfermedad

### 4. **pacientes**
Información demográfica de los pacientes.
- `documento_identidad`: Cédula, pasaporte, etc.
- `eps`: Entidad Promotora de Salud
- `estado`: Activo, Inactivo, Fallecido

### 5. **paciente_enfermedades** (Tabla pivote)
Relación N:N entre pacientes y enfermedades.
- `es_principal`: Indica si es la enfermedad principal
- `fecha_diagnostico`: Fecha del diagnóstico

### 6. **historias_clinicas**
Registro de cada atención médica.
- `motivo_consulta`: Razón de la visita
- `impresion_diagnostica`: Diagnóstico del médico
- `plan_manipulacion`: Plan de tratamiento
- `estado`: Abierta, Cerrada, En seguimiento

### 7. **signos_vitales**
Mediciones del paciente en cada consulta.
- Presión arterial (sistólica/diastólica)
- Glucosa, peso, talla, IMC
- Frecuencia cardíaca y respiratoria

### 8. **citas**
Agendamiento de citas médicas.
- `recordatorio_enviado`: Control de notificaciones
- `estado`: Programada, Confirmada, Atendida, Cancelada

### 9. **medicamentos**
Catálogo de medicamentos disponibles.
- `es_controlado`: Indica si requiere receta especial

### 10. **prescripciones**
Tratamientos medicamentosos prescritos.
- `dosis`, `frecuencia`, `via_administracion`: Detalles de la prescripción
- `es_activo`: Permite suspend tratamientos

### 11. **examenes**
Solicitudes y resultados de exámenes.
- `archivo_resultado`: Ruta al PDF o imagen del resultado
- `estado`: Solicitado, En Proceso, Completado

### 12. **configuracion_alertas**
Umbrales para generar alertas automáticas.
- `valor_minimo`, `valor_maximo`: Rango aceptable
- `nivel_alerta`: Bajo, Medio, Alto

### 13. **alertas**
Alertas generadas por el sistema.
- Se crean automáticamente al registrar signos vitales fuera de rango
- `es_leida`: Control de atención de la alerta

### 14. **audit_logs**
Registro de auditoría de todas las acciones.
- Rastrea quién hizo qué y cuándo
- Cumple requisitos de seguridad y ley de protección de datos

## 🔗 Relaciones Principales

| Relación | Tipo | Descripción |
|----------|------|-------------|
| roles → usuarios | 1:N | Un rol tiene muchos usuarios |
| pacientes → historias_clinicas | 1:N | Un paciente tiene muchas historias |
| pacientes → citas | 1:N | Un paciente tiene muchas citas |
| pacientes → signos_vitales | 1:N | Un paciente tiene muchos registros |
| historias_clinicas → signos_vitales | 1:N | Cada historia tiene sus signos |
| historias_clinicas → prescripciones | 1:N | Cada historia tiene sus prescripciones |
| medicamentos → prescripciones | 1:N | Un medicamento se prescribe muchas veces |
| tipos_enfermedades → configuracion_alertas | 1:N | Cada enfermedad tiene sus umbrales |

## 🚀 Comandos para Crear la Base de Datos

```bash
# 1. Configurar .env con tus credenciales de MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seguimiento_pacientes
DB_USERNAME=usuario
DB_PASSWORD=contraseña

# 2. Crear la base de datos
mysql -u usuario -p -e "CREATE DATABASE seguimiento_pacientes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Ejecutar migraciones
php artisan migrate

# 4. (Opcional) Seeders para datos iniciales
php artisan db:seed
```

## 📈 Índices Implementados

- **pacientes**: idx_paciente_apellidos, idx_paciente_estado
- **historias_clinicas**: idx_hc_paciente_fecha, idx_hc_medico
- **signos_vitales**: idx_sv_paciente_fecha
- **citas**: idx_cita_fecha_estado, idx_cita_medico, idx_cita_paciente
- **alertas**: idx_alerta_paciente_leida, idx_alerta_severidad, idx_alerta_fecha

## 🔒 Consideraciones de Seguridad

1. **Soft Deletes**: Todas las tablas principales usan eliminación suave para auditoría
2. **Audit Logs**: Registro completo de todas las operaciones CRUD
3. **Foreign Keys**: Integridad referencial garantizada
4. **Datos Sensibles**: Las historias clínicas deben cifrarse a nivel de aplicación

## 📊 Tamaño Estimado de la Base de Datos

| Tabla | Registros/año estimado | Tamaño |
|-------|----------------------|--------|
| pacientes | 500-1,000 | ~500 KB |
| historias_clinicas | 5,000-10,000 | ~5 MB |
| signos_vitales | 20,000-30,000 | ~10 MB |
| citas | 10,000-15,000 | ~2 MB |
| alertas | 5,000-8,000 | ~1 MB |
| **Total anual** | | **~20 MB** |
