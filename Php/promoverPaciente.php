<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Recoger datos ────────────────────────────────────────────────────────────
$cont     = isset($_POST['cont'])     ? (int)$_POST['cont']     : 0;
$redirect = $_POST['redirect'] ?? 'dashboard_recepcionista_promover_paciente.php';

// Sanitizar redirect
$redirect = preg_replace('/[^a-zA-Z0-9_.?\-=&\/]/', '', $redirect);

function redirigirError(string $msg, string $redirect): void {
    $_SESSION['recep_error'] = $msg;
    header('Location: ../dashboard/' . $redirect);
    exit;
}

// ── Validación básica ────────────────────────────────────────────────────────
if ($cont <= 0) {
    redirigirError('Datos inválidos. No se pudo realizar la promoción.', $redirect);
}

// ── Verificar que el usuario existe y tiene rol 'na' ────────────────────────
$stmt = $conn->prepare(
    "SELECT cont, nameUser, secondNameUser, rolUser FROM usuario WHERE cont = ?"
);
$stmt->bind_param('i', $cont);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    redirigirError('El usuario no existe.', $redirect);
}

$usuario = $res->fetch_assoc();

if ($usuario['rolUser'] !== 'na') {
    redirigirError(
        'Este usuario no puede ser promovido: su rol actual no es "Visitante".',
        $redirect
    );
}

// ── Actualizar rol a 'paciente' ──────────────────────────────────────────────
$upd = $conn->prepare("UPDATE usuario SET rolUser = 'paciente' WHERE cont = ?");
$upd->bind_param('i', $cont);

if ($upd->execute()) {
    $nombre = htmlspecialchars($usuario['nameUser'] . ' ' . $usuario['secondNameUser']);
    $_SESSION['recep_exito'] = "\"$nombre\" ha sido promovido a Paciente correctamente.";
} else {
    redirigirError('Error al actualizar el rol. Intenta de nuevo.', $redirect);
}

$conn->close();
header('Location: ../dashboard/' . $redirect);
exit;
