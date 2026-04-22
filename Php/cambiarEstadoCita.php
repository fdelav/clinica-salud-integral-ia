<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Validación básica ────────────────────────────────────────────────────────
$contCita    = isset($_POST['contCita'])    ? (int)$_POST['contCita']    : 0;
$nuevoEstado = $_POST['nuevoEstado'] ?? '';
$redirect    = $_POST['redirect']    ?? 'dashboard_recepcionista_ver_citas.php';

// Sanitizar redirect (solo rutas internas relativas)
$redirect = preg_replace('/[^a-zA-Z0-9_.?\-=&\/]/', '', $redirect);

$estadosPermitidos = ['pendiente', 'confirmada', 'completada', 'cancelada'];

if ($contCita <= 0 || !in_array($nuevoEstado, $estadosPermitidos, true)) {
    $_SESSION['recep_error'] = 'Datos inválidos. No se pudo cambiar el estado.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

// ── Verificar que la cita existe y obtener su estado actual ─────────────────
$stmt = $conn->prepare('SELECT estadoCita FROM citas WHERE contCita = ?');
$stmt->bind_param('i', $contCita);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['recep_error'] = 'La cita no existe.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

$cita = $res->fetch_assoc();

// No permitir cambiar si ya está cancelada o completada (doble check server-side)
if (in_array($cita['estadoCita'], ['cancelada', 'completada'], true) && $nuevoEstado !== $cita['estadoCita']) {
    $_SESSION['recep_error'] = 'No se puede cambiar el estado de una cita cancelada o completada.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

// ── Actualizar ───────────────────────────────────────────────────────────────
$upd = $conn->prepare('UPDATE citas SET estadoCita = ? WHERE contCita = ?');
$upd->bind_param('si', $nuevoEstado, $contCita);

if ($upd->execute()) {
    $_SESSION['recep_exito'] = 'Estado de la cita actualizado a "' . ucfirst($nuevoEstado) . '" correctamente.';
} else {
    $_SESSION['recep_error'] = 'Error al actualizar el estado. Intenta de nuevo.';
}

$conn->close();
header('Location: ../dashboard/' . $redirect);
exit;
