<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Recoger y sanear datos ───────────────────────────────────────────────────
$fechaCita      = trim($_POST['fechaCita']      ?? '');
$horaInicioCita = trim($_POST['horaInicioCita'] ?? '');
$horaFinalCita  = trim($_POST['horaFinalCita']  ?? '');
$contDoctor     = isset($_POST['contDoctor'])   ? (int)$_POST['contDoctor'] : 0;
$lugarCita      = trim($_POST['lugarCita']      ?? '');
$motivoCita     = trim($_POST['motivoCita']     ?? '');

// Guardar datos para repoblar el form en caso de error
$_SESSION['recep_form_data'] = [
    'fechaCita'      => $fechaCita,
    'horaInicioCita' => $horaInicioCita,
    'horaFinalCita'  => $horaFinalCita,
    'contDoctor'     => $contDoctor,
    'lugarCita'      => $lugarCita,
    'motivoCita'     => $motivoCita,
];

// ── Validaciones ─────────────────────────────────────────────────────────────
function redirigirError(string $msg): void {
    $_SESSION['recep_error'] = $msg;
    header('Location: ../dashboard/dashboard_recepcionista_crear_cita.php');
    exit;
}

if ($fechaCita === '' || $horaInicioCita === '' || $horaFinalCita === '') {
    redirigirError('La fecha y los horarios son obligatorios.');
}

// Fecha no puede ser anterior a hoy
if ($fechaCita < date('Y-m-d')) {
    redirigirError('La fecha de la cita no puede ser anterior a hoy.');
}

// Hora fin debe ser mayor a hora inicio
if ($horaFinalCita <= $horaInicioCita) {
    redirigirError('La hora de fin debe ser posterior a la hora de inicio.');
}

if ($contDoctor <= 0) {
    redirigirError('Debes seleccionar un doctor.');
}

if ($lugarCita === '') {
    redirigirError('El lugar / consultorio es obligatorio.');
}
if (mb_strlen($lugarCita) > 150) {
    redirigirError('El lugar no puede superar los 150 caracteres.');
}
if (mb_strlen($motivoCita) > 255) {
    redirigirError('El motivo no puede superar los 255 caracteres.');
}

// ── Verificar que el doctor existe y tiene rol 'doctor' ─────────────────────
$stmtDoc = $conn->prepare(
    "SELECT cont FROM usuario WHERE cont = ? AND rolUser = 'doctor'"
);
$stmtDoc->bind_param('i', $contDoctor);
$stmtDoc->execute();
if ($stmtDoc->get_result()->num_rows === 0) {
    redirigirError('El doctor seleccionado no es válido.');
}

// ── Verificar que el doctor no tenga otra cita solapada ese día y hora ───────
$stmtSolape = $conn->prepare(
    "SELECT contCita FROM citas
     WHERE contDoctor   = ?
       AND fechaCita    = ?
       AND estadoCita  != 'cancelada'
       AND horaInicioCita < ?
       AND horaFinalCita  > ?"
);
$stmtSolape->bind_param('isss', $contDoctor, $fechaCita, $horaFinalCita, $horaInicioCita);
$stmtSolape->execute();
if ($stmtSolape->get_result()->num_rows > 0) {
    redirigirError('El doctor ya tiene una cita en ese horario. Por favor elige otro horario.');
}

// ── Insertar ─────────────────────────────────────────────────────────────────
$motivoFinal = $motivoCita !== '' ? $motivoCita : null;

$ins = $conn->prepare(
    "INSERT INTO citas
        (fechaCita, horaInicioCita, horaFinalCita, lugarCita, motivoCita, estadoCita, contDoctor, contPaciente)
     VALUES (?, ?, ?, ?, ?, 'pendiente', ?, NULL)"
);
$ins->bind_param('sssssi', $fechaCita, $horaInicioCita, $horaFinalCita, $lugarCita, $motivoFinal, $contDoctor);

if ($ins->execute()) {
    unset($_SESSION['recep_form_data']);
    $_SESSION['recep_exito'] = 'Cita creada correctamente. Puedes asignar un paciente desde esta pantalla.';
    $conn->close();
    header('Location: ../dashboard/dashboard_recepcionista_ver_citas.php');
    exit;
} else {
    $conn->close();
    redirigirError('Error al guardar la cita. Intenta de nuevo.');
}
