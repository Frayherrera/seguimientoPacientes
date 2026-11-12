# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

`AGENTS.md` es la fuente principal (estado de módulos, reglas de negocio, gotchas, comandos, bugs conocidos) y se importa aquí. Mantener los hechos allí; este archivo solo agrega el mapa de arquitectura.

@AGENTS.md

## Comandos (resumen)

No hay PHP en el host: todo lo de PHP se corre en Docker.

- Tests: `docker run --rm -v "${PWD}:/app" -w /app php:8.3-cli php artisan test`
- Test único: añadir `--filter=CitaTest` (o un nombre de test) al comando anterior.
- Formato: mismo contenedor con `vendor/bin/pint` (correrlo antes de dar un cambio por terminado).
- App en Docker: `docker compose up -d --build` → Apache + PHP 8.3 en `http://localhost:8080` (DocumentRoot `public/`, código montado como volumen). Requiere Docker Desktop corriendo y una base MySQL accesible como `mysql_db` (compose no la define).
- Frontend: `npm run dev` / `npm run build` (Vite + Tailwind v3 vía PostCSS).

## Arquitectura

**Flujo de un módulo del panel** (`/dashboard/*`, todo bajo middleware `auth`, nombres de ruta `dashboard.<modulo>[.accion]`):
1. `routes/web.php` declara las rutas a mano (no `Route::resource`). El nombre base `dashboard.<modulo>` es la página índice y lo usa el sidebar de `layouts/dashboard.blade.php`.
2. El controller valida filtros de `index` inline con `$request->validate(...)` y las escrituras con FormRequests (`app/Http/Requests/{Store,Update}<Modelo>Request.php`).
3. `index` devuelve **una sola vista Blade por módulo** (`resources/views/dashboard/<modulo>.blade.php`, extiende `layouts.dashboard` con `@section('content')`) con listado paginado (`->paginate(10)->withQueryString()`), estadísticas y datos para los selects.
4. Crear/editar ocurre en **modales dentro de la misma vista** (JS/Alpine; las URLs se inyectan con `@json(route(...))`), y los formularios hacen POST/PUT clásico con redirect + flash. Los endpoints `show` de algunos módulos devuelven JSON para llenar modales de detalle.
5. Reglas de estado/transición viven como constantes en el controller (p. ej. `CitaController::TRANSICIONES`).

**Dominio** (`app/Models`, esquema en `database/DATABASE_DESIGN.md`):
- `User` (tabla `users`, columna `nombre`, `es_activo`) → `Role`. Los médicos son usuarios con rol `'Médico'`.
- `Paciente` ↔ `TipoEnfermedad` vía `PacienteEnfermedad`.
- `HistoriaClinica` (un control médico) → `SignoVital`, `Prescripcion` (→ `Medicamento`), `Examen`, `Alerta`.
- `ConfiguracionAlerta`: umbrales min/max por enfermedad + parámetro + nivel; los consume `app/Services/GeneradorDeAlertas.php` al registrar/editar un control en `SeguimientoController`.
- `Cita` (paciente + médico, fecha/hora, estado). `AuditLog` existe en el esquema.

**Auth**: Breeze session-based (`routes/auth.php`, `app/Http/Controllers/Auth`, vistas `auth/*` con `layouts/guest`). El scaffold de Breeze no se adaptó del todo a la tabla `users` custom; ese es el origen de los 7 tests fallidos preexistentes.

**Tests**: Pest, un archivo Feature por módulo en `tests/Feature/`, SQLite en memoria + `RefreshDatabase`. Siguen el patrón actuar como usuario → golpear la ruta nombrada → afirmar BD/redirect/JSON.
