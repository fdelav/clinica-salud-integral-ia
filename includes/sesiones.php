<?php
/**
 * sesiones.php — Gestión central de sesiones
 * Ubicación en el proyecto: Php/sesiones.php
 *
 * Uso en cada dashboard:
 *   require_once '../Php/sesiones.php';
 *   verificarSesion();
 *   verificarRol(['admin']);          // solo admins
 *   verificarRol(['doctor','admin']); // varios roles permitidos
 *   $usuario = obtenerUsuario();
 */

// ── Roles válidos en el sistema ──────────────────────────────────────────────
const ROLES_VALIDOS = ['na', 'doctor', 'enfermero', 'secretario', 'diseñador', 'admin'];

// ── URLs de redirección ──────────────────────────────────────────────────────
const URL_LOGIN  = '../Html/login.html';
const URL_INICIO = '../index.php';

/**
 * Inicia la sesión si aún no está activa.
 * Llamar siempre al principio de cada archivo que use sesiones.
 */
function iniciarSesion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica que existe una sesión activa.
 * Si no hay usuario logueado, redirige al login.
 */
function verificarSesion(): void
{
    iniciarSesion();

    if (empty($_SESSION['usuario'])) {
        header('Location: ' . URL_LOGIN);
        exit;
    }
}

/**
 * Verifica que el rol del usuario logueado esté entre los roles permitidos.
 * Llama a verificarSesion() internamente, así que no hace falta llamarla antes.
 *
 * @param string[] $rolesPermitidos  Array con los roles que pueden acceder.
 *                                   Ejemplo: ['admin'], ['doctor', 'enfermero']
 * @param string   $redireccion      URL a la que ir si el rol no está permitido.
 *                                   Por defecto vuelve al inicio.
 */
function verificarRol(array $rolesPermitidos, string $redireccion = URL_INICIO): void
{
    verificarSesion();

    $rolActual = $_SESSION['rol'] ?? '';

    if (!in_array($rolActual, $rolesPermitidos, true)) {
        header('Location: ' . $redireccion);
        exit;
    }
}

/**
 * Devuelve un array con los datos del usuario actualmente logueado.
 * Si no hay sesión activa devuelve null.
 *
 * Claves disponibles (según lo que guarde autentificacion.php en $_SESSION):
 *   'id'       → ID del usuario en la BD
 *   'usuario'  → nombre de usuario / email
 *   'nombre'   → nombre completo
 *   'rol'      → rol del sistema (admin, doctor, etc.)
 *
 * @return array<string,mixed>|null
 */
function obtenerUsuario(): ?array
{
    iniciarSesion();

    if (empty($_SESSION['usuario'])) {
        return null;
    }

    return [
        'id'      => $_SESSION['id']      ?? null,
        'usuario' => $_SESSION['usuario'] ?? '',
        'nombre'  => $_SESSION['nombre']  ?? '',
        'rol'     => $_SESSION['rol']     ?? '',
    ];
}

/**
 * Cierra la sesión activa y redirige al inicio.
 * cerrar_sesion.php puede simplemente llamar a esta función.
 *
 * @param string $redireccion  URL destino tras cerrar sesión.
 */
function cerrarSesion(string $redireccion = URL_INICIO): void
{
    iniciarSesion();
    session_unset();
    session_destroy();
    header('Location: ' . $redireccion);
    exit;
}
