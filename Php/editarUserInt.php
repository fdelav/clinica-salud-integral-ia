<?php
require_once '../includes/sesiones.php';
verificarRol(['admin']);

// ── Recoger datos del POST ────────────────────────────────────────────────────
$idUser_original  = $_POST['idUser_original']  ?? '';
$tipoId_original  = $_POST['tipoId_original']  ?? '';

$nameUser         = htmlspecialchars(trim($_POST['nameUser']          ?? ''));
$secondNameUser   = htmlspecialchars(trim($_POST['secondNameUser']    ?? ''));
$tipoId           = htmlspecialchars(trim($_POST['tipoId']            ?? ''));
$idUser           = htmlspecialchars(trim($_POST['idUser']            ?? ''));
$fechaNacimiento  = htmlspecialchars(trim($_POST['fechaNacimientoUsr']?? ''));
$generoUser       = htmlspecialchars(trim($_POST['generoUser']        ?? ''));
$emailUser        = htmlspecialchars(trim($_POST['emailUser']         ?? ''));
$telUser          = htmlspecialchars(trim($_POST['telUser']           ?? ''));
$rolUser          = htmlspecialchars(trim($_POST['rolUser']           ?? ''));
$passwordUser     = $_POST['passwordUser']     ?? '';
$repeatPassword   = $_POST['repeatPasswordUser'] ?? '';

// URL de retorno al dashboard con los mismos parámetros de búsqueda
$urlRetorno = "dashboard_admin_editar_usuario.php?buscar=1&busq_tipoId=$tipoId_original&busq_idUser=" . urlencode($idUser_original);

// ── Validaciones ─────────────────────────────────────────────────────────────

// Campos obligatorios
if (!$nameUser || !$secondNameUser || !$tipoId || !$idUser || !$emailUser || !$rolUser) {
    $_SESSION['editar_error'] = 'Por favor completa todos los campos obligatorios.';
    header("Location: $urlRetorno");
    exit;
}

// Validar contraseña solo si se quiere cambiar
$cambiarPassword = false;
if ($passwordUser !== '') {
    if ($passwordUser !== $repeatPassword) {
        $_SESSION['editar_error'] = 'Las contraseñas no coinciden.';
        header("Location: $urlRetorno");
        exit;
    }
    if (strlen($passwordUser) < 6) {
        $_SESSION['editar_error'] = 'La contraseña debe tener al menos 6 caracteres.';
        header("Location: $urlRetorno");
        exit;
    }
    $cambiarPassword = true;
}

// ── Conexión y UPDATE ─────────────────────────────────────────────────────────
require '../Php/coneccion.php';

if ($cambiarPassword) {
    $passwordHash = password_hash($passwordUser, PASSWORD_DEFAULT);
    $sql = "UPDATE usuario SET
                nameUser            = '$nameUser',
                secondNameUser      = '$secondNameUser',
                tipoId              = '$tipoId',
                idUser              = '$idUser',
                fechaNacimientoUsr  = '$fechaNacimiento',
                generoUser          = '$generoUser',
                emailUser           = '$emailUser',
                telUser             = '$telUser',
                rolUser             = '$rolUser',
                passwordUser        = '$passwordHash'
            WHERE tipoId = '$tipoId_original' AND idUser = '$idUser_original'";
} else {
    $sql = "UPDATE usuario SET
                nameUser            = '$nameUser',
                secondNameUser      = '$secondNameUser',
                tipoId              = '$tipoId',
                idUser              = '$idUser',
                fechaNacimientoUsr  = '$fechaNacimiento',
                generoUser          = '$generoUser',
                emailUser           = '$emailUser',
                telUser             = '$telUser',
                rolUser             = '$rolUser'
            WHERE tipoId = '$tipoId_original' AND idUser = '$idUser_original'";
}

if ($conn->query($sql)) {
    error_log("usuario modificado: ". $tipoId_original);
    $_SESSION['editar_exito'] = 'Usuario actualizado correctamente.';
    // Si cambió el ID o tipoId, actualizar la URL de retorno
    $urlRetorno = "../dashboard/dashboard_admin_editar_usuario.php?buscar=1&busq_tipoId=$tipoId&busq_idUser=" . urlencode($idUser);
} else {
    $_SESSION['editar_error'] = 'Error al actualizar: ' . $conn->error;
}

$conn->close();
header("Location: $urlRetorno");
exit;
