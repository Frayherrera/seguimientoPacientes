# AGENTS.md

## Qué es este repo

Sistema de seguimiento de pacientes crónicos (clínica) en **Laravel 12 + PHP 8.2**. Docs y UI en español.

Estado real del código (importante):
- **Backend funcional**: auth (Breeze session-based), perfil de usuario, y los módulos **Pacientes**, **Enfermedades**, **Usuarios**, **Seguimiento Médico**, **Historial Clínico** y **Citas** (controllers en `app/Http/Controllers/{PacienteController,EnfermedadController,UsuarioController,SeguimientoController,HistorialController,CitaController}.php` con rutas reales bajo `/dashboard/*`). Reglas de negocio ya establecidas: borrar enfermedad con pacientes asignados está bloqueado (sugerir desactivar); el usuario no puede eliminarse ni desactivarse a sí mismo; contraseña vacía al editar = mantener la actual. Seguimiento: registrar un control crea historia clínica + signos vitales (IMC se calcula solo en el hook de `SignoVital`), y `app/Services/GeneradorDeAlertas.php` evalúa los valores contra `configuracion_alertas` de las enfermedades del paciente generando una `Alerta` por parámetro fuera de rango (manda la severidad más alta: Alto→Crítico, Medio→Advertencia, Bajo→Info); editar un control regenera sus alertas (borra las 'Valor fuera de rango' previas de esa historia); el paciente de una historia clínica es inmutable; fecha de atención no puede ser futura; medico_id = usuario autenticado; no hay borrado de controles (registros clínicos); peso/talla se validan en rangos plausibles (0.5-500 kg / 30-250 cm) y la combinación que produzca IMC > 999.99 se rechaza en validación (la columna `imc` es `decimal(5,2)`; el hook de `SignoVital` además no asigna IMC fuera de ese rango porque MySQL rechaza el INSERT). Historial Clínico: módulo **solo lectura** (la creación vive en Seguimiento) con stats, filtros server-side (búsqueda por paciente/diagnóstico/médico, período, médico), modal de detalle JSON (`/historial/{id}` incluye signos, prescripciones y `proxima_cita`) y exportación CSV `/historial/export` que respeta los filtros. Citas: CRUD con **flujo de estados** `Programada → Confirmada → Atendida` (más Cancelada y No Presentado desde Programada/Confirmada; Atendida/Cancelada/No Presentado son terminales y no editables); nacen como Programada; fecha no puede ser pasada; el médico no puede tener dos citas en la misma fecha/hora (las canceladas/no presentadas no bloquean el slot); médicos para el select = usuarios activos con rol 'Médico'; filtros server-side (búsqueda, estado, médico, período próximas/hoy/pasadas/todas). Reportes: **análisis con datos reales** de Pacientes, Citas, Enfermedades (prevalencia) y Seguimiento (controles + alertas por severidad), con filtro de período (mes/trimestre/año/personalizado con desde/hasta), gráficos CSS y **exportación CSV** `/reportes/export?tipo={resumen|pacientes|citas|enfermedades|seguimiento}` (BOM UTF-8, ';'); las tarjetas de reportes Financiero/Calidad, "Reportes Recientes" y "Programar Reporte" del prototipo se eliminaron por no tener dominio. Configuración: **CRUD de umbrales** de `configuracion_alertas` (único por enfermedad+parámetro+nivel_alerta; parámetros limitados a `GeneradorDeAlertas::PARAMETROS`; validado min<=max; desactivar/eliminar un umbral no afecta alertas ya generadas), **perfil del usuario autenticado** (contraseña vacía = mantener la actual; si cambia requiere la contraseña actual) y **cerrar sesión en otros dispositivos** (`Auth::logoutOtherDevices` con contraseña actual); las secciones de notificaciones/respaldos/integraciones/idioma del prototipo se eliminaron por no tener dominio persistente.
- **Pendiente**: ya no quedan prototipos `/dashboard/*` sin conectar. Sigue modelado sin controller: **Examen** (migración, modelo y relaciones en Paciente/HistoriaClinica, pero sin CRUD ni uso en Seguimiento). Patrón de módulo (por si se extiende): controller + FormRequests + rutas reales reemplazando `Route::view` (manteniendo el nombre de ruta, lo usa el sidebar) + vista con datos reales + tests Pest (ver `tests/Feature/{PacienteTest,EnfermedadTest,UsuarioTest,SeguimientoTest,HistorialTest,CitaTest,ReporteTest,ConfiguracionTest}.php`).
- **Gotchas de modelos**: la tabla `users` usa `nombre` (singular, no `nombres`); modelos sin `$table` que Laravel pluraliza mal en inglés (ya todos corregidos: `HistoriaClinica`→`historias_clinicas`, `SignoVital`→`signos_vitales`, `Prescripcion`→`prescripciones`, `Examen`→`examenes`); Eloquent serializa las relaciones camelCase a snake_case en JSON (`setRelation('proximaCita', …)` aparece como `proxima_cita`). Columnas con cast `'date'` (p. ej. `fecha_atencion`) se **guardan como `'Y-m-d 00:00:00'` en SQLite**: filtrar con `whereDate`, nunca `whereBetween` con strings de fecha (bug que vació el CSV de seguimiento en Reportes). `GeneradorDeAlertas::PARAMETROS` es público (lo usa Configuración para el select de parámetros); `HistoriaClinica` tiene la relación `alertas()`; `ConfiguracionAlerta` **no** tiene SoftDeletes (el delete es real).
- **Tests**: correrlos en Docker (no hay PHP en el host): `docker run --rm -v "${PWD}:/app" -w /app php:8.3-cli php artisan test`. `vendor/bin/pint` igual dentro del container.
- Suite: Paciente (10) + Enfermedad (10) + Usuario (10) + Seguimiento (16) + Historial (9) + Citas (13) + Reportes (9) + Configuración (14) = 91 pasan. **7 fallos son preexistentes** del scaffold Breeze sin adaptar a los usuarios custom (faltan `Route [dashboard]`, Breeze usa columna `name`, hard delete vs soft delete) — verificado con git stash; no introducirlos como regresión pero tampoco "arreglarlos" sin preguntar.

## Comandos

- `composer dev` — arranca todo a la vez (serve, queue:listen, pail, vite) vía concurrently. Usar este, no `php artisan serve` suelto.
- `composer test` — `config:clear` + `php artisan test` (el clear previo importa; sin él pueden fallar tests por config cacheada).
- Test único: `php artisan test --filter=NombreTest` o `./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php`.
- Formato: `./vendor/bin/pint` (Laravel Pint instalado; no hay CI que lo corra, ejecutarlo antes de dar por hecho un cambio).
- Frontend: `npm run dev` (Vite 7) / `npm run build`.

## Base de datos

- `phpunit.xml` fuerza **SQLite en memoria** para tests (RefreshDatabase vía `tests/Pest.php`). Los tests no necesitan MySQL.
- `.env` apunta a **MySQL en host `mysql_db`** (nombre de servicio Docker), pero `docker-compose.yml` solo define el contenedor `app` en `:8080` — **no existe servicio de base de datos en compose**. Levantar MySQL aparte o ajustar compose antes de correr la app en local/Docker.
- `schema design: database/DATABASE_DESIGN.md` es la fuente de referencia del esquema (ER completo en español). `database/create_database.sql` es el SQL crudo alternativo.
- Seeders: `RolesSeeder`, `TiposEnfermedadesSeeder`, `UsersSeeder`, `MedicamentosSeeder`, `ConfiguracionAlertasSeeder` (todos en `DatabaseSeeder`). Usarlos al poblar datos de prueba.
- Gotchas de `.env`: `DB_USERNAME=Laravel ` tiene **espacio final**; no hay `.env.example` commiteado aunque el script `composer setup` lo asume.

## Frontend

- Tailwind **v3 vía PostCSS** (`tailwind.config.js` + `postcss.config.js` + directivas `@tailwind` en `resources/css/app.css`). `@tailwindcss/vite` (v4) está en package.json pero **no está cableado** en `vite.config.js` — no usarlo ni "migrar" a v4 por accidente.
- Alpine.js para interactividad; iconos FontAwesome en las vistas Blade.
- Layouts: `layouts/app` (Breeze), `layouts/dashboard` (páginas del panel), `layouts/guest`.
- **Cuando se necesite trabajo de diseño (UI/visual), usar la skill `frontend-design`** antes de generar o rediseñar vistas.

## Rutas y bugs conocidos

`routes/web.php` registra vistas inexistentes — existen solo las vistas listadas en `resources/views/`:
- `Route::view('/login', 'login')` apunta a una vista `login` que **no existe** (la real es `auth.login` de Breeze) y además **colide con la ruta `login` de Breeze** en `routes/auth.php` (misma URL y nombre). GET `/login` falla hoy.
- Rutas `/demo/solicitar`, `/contacto/ventas` y `/buscar` referencian vistas `demo`, `contacto-ventas` y `buscar` que no existirán hasta crearlas.

No arreglar estas rutas "por passes de lado": decidir con el usuario si se crean las vistas o se eliminan las rutas.
