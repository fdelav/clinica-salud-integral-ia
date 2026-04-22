# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP-based clinic management system ("Clínica Salud Integral") with role-based dashboards. No framework — pure PHP with procedural structure.

## Database

**Connection**: `Php/coneccion.php` — MariaDB/MySQL, database `csif`, user `root`, no password.

**Schema**: `Sql/usuario.sql` defines the `usuario` table with fields: `cont` (auto-increment PK), `nameUser`, `secondNameUser`, `tipoID`, `idUser`, `fechaNacimientoUsr`, `generoUser`, `emailUser`, `passwordUser` (hashed with `password_verify`), `telUser`, `rolUser`, `cont`.

Other tables referenced in dashboards: `citas`, `historias_medicas` (created separately, not in the provided SQL dump).

## Roles

| DB value | Session `$_SESSION['rol']` |
|----------|---------------------------|
| `admin` | `admin` |
| `doctor` | `doctor` |
| `recep` | `recepcionista` |
| `paciente` | `paciente` |

Dashboard routing in `index.php:9-20` switches on `$_SESSION['rol']` to redirect to the appropriate dashboard under `dashboard/`.

## Session Management

All session logic centralized in `includes/sesiones.php`:

- `iniciarSesion()` — starts session if none active
- `verificarSesion()` — redirects to `../Html/login.html` if no user (note: actual login page is `Html/login.php`)
- `verificarRol(['admin', ...])` — checks role, redirects to `../index.php` on failure
- `obtenerUsuario()` — returns `['id', 'usuario', 'nombre', 'rol']` or `null`
- `cerrarSesion()` — destroys session and redirects

**Important**: the `URL_LOGIN` constant in `sesiones.php` points to `../Html/login.html` but the actual login page is `Html/login.php`. This may need correction if login redirects behave unexpectedly.

## Authentication Flow

1. `Html/login.php` posts to `Php/autentificacion.php`
2. `autentificacion.php` queries `usuario` by email, verifies password with `password_verify()`, then sets `$_SESSION['usuario']`, `$_SESSION['nombre']`, `$_SESSION['id']`, `$_SESSION['rol']`
3. Redirects to `../index.php`

## Dashboard Structure

Each dashboard lives under `dashboard/` and includes its sidebar from `includes/sidebar_<role>.php`. The sidebar sets `$paginaActual` to highlight the active nav item.

| Dashboard | File | Sidebar |
|-----------|------|---------|
| Admin | `dashboard_admin.php` | `sidebar_admin.php` |
| Doctor | `dashboard_doctor.php` | `sidebar_doctor.php` |
| Recepcionista | `dashboard_recepcionista.php` | `sidebar_recepcionista.php` |
| Paciente | `dashboard_paciente.php` | `sidebar_paciente.php` |

## Key Files

- `index.php` — public landing page; shows login/register or dashboard link based on session
- `Html/login.php` — login form posting to `../Php/autentificacion.php`
- `Html/registro.html` — registration form (static HTML, posts to `Php/registroUserInt.php`)
- `Php/autentificacion.php` — authentication handler
- `Php/registroUserInt.php` — user registration
- `Php/editarUserInt.php` — user edit
- `Php/cambiarEstadoCita.php` — change appointment status
- `Php/crearCita.php` — create appointment
- `includes/sidebar_*.php` — role-specific sidebar navigation
- `Css/my_style.css` — public pages styling
- `Css/dashboard.css` — shared dashboard styling

## Styling

Bootstrap 5 via CDN + Google Fonts (Montserrat, Nunito). Custom CSS in `Css/my_style.css` and `Css/dashboard.css` with role-specific variants (e.g., `Css/dashboard_doctor.css`). Bootstrap Icons from CDN.

## Dependencies

No Composer or build step. External resources loaded from CDN:
- `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
- `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css`
- `https://fonts.googleapis.com/css2?family=Montserrat:...`