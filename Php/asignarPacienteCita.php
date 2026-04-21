<?php
require_once '../includes/sesiones.php';
verificarRol(['secretario']);

require '../Php/coneccion.php';

// ── Validación básica ────────────────────────────────────────────────────────
$contCita     = isset($_POST['contCita'])     ? (int)$_POST['contCita']     : 0;
$contPaciente = isset($_POST['contPaciente']) ? (int)$_POST['contPaciente'] : 0;
$redirect     = $_POST['redirect'] ?? 'dashboard_recepcionista_ver_citas.php';

// Sanitizar redirect
$redirect = preg_replace('/[^a-zA-Z0-9_.?\-=&\/]/', '', $redirect);

if ($contCita <= 0 || $contPaciente <= 0) {
    $_SESSION['recep_error'] = 'Datos inválidos. No se pudo asignar el paciente.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

// ── Verificar que la cita existe y no está cancelada/completada ─────────────
$stmtCita = $conn->prepare('SELECT estadoCita FROM citas WHERE contCita = ?');
$stmtCita->bind_param('i', $contCita);
$stmtCita->execute();
$resCita = $stmtCita->get_result();

if ($resCita->num_rows === 0) {
    $_SESSION['recep_error'] = 'La cita no existe.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

$cita = $resCita->fetch_assoc();
if (in_array($cita['estadoCita'], ['cancelada', 'completada'], true)) {
    $_SESSION['recep_error'] = 'No se puede asignar un paciente a una cita cancelada o completada.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

// ── Verificar que el paciente existe y tiene rol 'paciente' ─────────────────
$stmtPac = $conn->prepare(
    "SELECT cont, nameUser, secondNameUser FROM usuario WHERE cont = ? AND rolUser = 'paciente'"
);
$stmtPac->bind_param('i', $contPaciente);
$stmtPac->execute();
$resPac = $stmtPac->get_result();

if ($resPac->num_rows === 0) {
    $_SESSION['recep_error'] = 'El paciente seleccionado no es válido.';
    header('Location: ../dashboard/' . $redirect);
    exit;
}

$paciente = $resPac->fetch_assoc();

// ── Asignar ──────────────────────────────────────────────────────────────────
$upd = $conn->prepare('UPDATE citas SET contPaciente = ? WHERE contCita = ?');
$upd->bind_param('ii', $contPaciente, $contCita);

if ($upd->execute()) {
    $nombrePac = htmlspecialchars($paciente['nameUser'] . ' ' . $paciente['secondNameUser']);
    $_SESSION['recep_exito'] = "Paciente \"$nombrePac\" asignado a la cita correctamente.";
} else {
    $_SESSION['recep_error'] = 'Error al asignar el paciente. Intenta de nuevo.';
}

$conn->close();
header('Location: ../dashboard/' . $redirect);
exit;
