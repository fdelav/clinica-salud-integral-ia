<?php
require_once 'coneccion.php';

$idUser = $_POST['idUser'] ?? '';
$tipoId = $_POST['tipoId'] ?? '';

if ($idUser === '' || $tipoId === '') {
    $_SESSION['admin_error'] = 'Datos incompletos. No se pudo eliminar el usuario.';
    header('Location: ../dashboard/dashboard_admin_ver_usuarios.php');
    exit;
}

$stmt = $conn->prepare("DELETE FROM usuario WHERE tipoId = ? AND idUser = ?");
$stmt->bind_param('ss', $tipoId, $idUser);
$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    $_SESSION['admin_exito'] = 'Usuario eliminado correctamente.';
} else {
    $_SESSION['admin_error'] = 'Error al eliminar el usuario: ' . $conn->error;
}
header('Location: ../dashboard/dashboard_admin_ver_usuarios.php');
exit;