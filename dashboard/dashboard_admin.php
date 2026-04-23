<?php
require_once '../includes/sesiones.php';
verificarRol(['admin']);

require '../Php/coneccion.php';

// ── Estadísticas ───────────────────────────────────────────────────────────────
$hoy = date('Y-m-d');

// Total usuarios
$resTotal = $conn->query("SELECT COUNT(*) AS total FROM usuario");
$totalUsuarios = $resTotal->fetch_assoc()['total'] ?? 0;

// Doctores activos (rol = 'doctor')
$resDoctores = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rolUser = 'doctor'");
$totalDoctores = $resDoctores->fetch_assoc()['total'] ?? 0;

// Citas de hoy
$resCitasHoy = $conn->query("SELECT COUNT(*) AS total FROM citas WHERE fechaCita = '$hoy'");
$citasHoy = $resCitasHoy->fetch_assoc()['total'] ?? 0;

// Administradores
$resAdmins = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rolUser = 'admin'");
$totalAdmins = $resAdmins->fetch_assoc()['total'] ?? 0;



$conn->close();

function badgeEstado(string $estado): string {
    return match($estado) {
        'pendiente'  => 'background:#fff3cd; color:#856404;',
        'confirmada' => 'background:#d1e7dd; color:#0f5132;',
        'cancelada'  => 'background:#f8d7da; color:#842029;',
        'completada' => 'background:#cfe2ff; color:#084298;',
        default      => 'background:#e2e3e5; color:#41464b;',
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrador — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'inicio'; include '../includes/sidebar_admin.php'?>

    <!-- ══════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════ -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-grid-fill" style="color:var(--dorado); margin-right:8px;"></i>Inicio</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <!-- Área de contenido -->
        <div class="content-area">

            <div class="welcome-header">
                <h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?></h1>
                <p>Resumen general del sistema — Clínica Salud Integral · <?= date('d/m/Y') ?></p>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-value"><?= $totalUsuarios ?></div>
                    <div class="stat-label">Usuarios registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="stat-value"><?= $totalDoctores ?></div>
                    <div class="stat-label">Doctores activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <div class="stat-value"><?= $citasHoy ?></div>
                    <div class="stat-label">Citas hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div class="stat-value"><?= $totalAdmins ?></div>
                    <div class="stat-label">Administradores</div>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <h3 class="section-title"><i class="bi bi-lightning-fill"></i> Accesos rápidos</h3>
            <div class="quick-grid">
                <a href="dashboard_admin_crear_usuario.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-person-plus-fill"></i></span>
                    <span class="qc-label">Crear usuario</span>
                </a>
                <a href="dashboard_admin_ver_usuarios.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="qc-label">Ver usuarios</span>
                </a>
                <a href="dashboard_admin_editar_usuario.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-person-gear"></i></span>
                    <span class="qc-label">Editar usuario</span>
                </a>
                <a href="dashboard_admin_eliminar_usuario.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-person-x-fill"></i></span>
                    <span class="qc-label">Eliminar usuario</span>
                </a>
            </div>
        </div>
    </main>

</body>
</html>
