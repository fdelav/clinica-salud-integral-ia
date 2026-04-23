<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Datos para las tarjetas de resumen ───────────────────────────────────────

// Citas de hoy
$hoy = date('Y-m-d');
$resCitasHoy = $conn->query("SELECT COUNT(*) AS total FROM citas WHERE fechaCita = '$hoy'");
$citasHoy = $resCitasHoy->fetch_assoc()['total'] ?? 0;

// Citas pendientes (sin paciente asignado)
$resSinPaciente = $conn->query("SELECT COUNT(*) AS total FROM citas WHERE contPaciente IS NULL AND estadoCita = 'pendiente'");
$citasSinPaciente = $resSinPaciente->fetch_assoc()['total'] ?? 0;

// Visitantes pendientes de promover (rol = 'na')
$resVisitantes = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rolUser = 'na'");
$visitantesPendientes = $resVisitantes->fetch_assoc()['total'] ?? 0;

// Total pacientes
$resPacientes = $conn->query("SELECT COUNT(*) AS total FROM usuario WHERE rolUser = 'paciente'");
$totalPacientes = $resPacientes->fetch_assoc()['total'] ?? 0;

// Próximas 5 citas de hoy con doctor asignado
$resProximas = $conn->query(
    "SELECT c.contCita, c.horaInicioCita, c.horaFinalCita, c.lugarCita, c.estadoCita,
            c.motivoCita, c.contPaciente,
            CONCAT(u.nameUser, ' ', u.secondNameUser) AS nombreDoctor
     FROM citas c
     LEFT JOIN usuario u ON u.cont = c.contDoctor
     WHERE c.fechaCita = '$hoy'
     ORDER BY c.horaInicioCita ASC
     LIMIT 5"
);

// Últimos 5 visitantes registrados
$resVisitantesRecientes = $conn->query(
    "SELECT nameUser, secondNameUser, emailUser, idUser, tipoId
     FROM usuario
     WHERE rolUser = 'na'
     ORDER BY `cont` DESC
     LIMIT 5"
);

$conn->close();

// Flash messages
$flashExito = $_SESSION['recep_exito'] ?? null;
$flashError = $_SESSION['recep_error'] ?? null;
unset($_SESSION['recep_exito'], $_SESSION['recep_error']);

// Helper: color badge según estado
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
    <title>Panel Recepcionista — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>
    <?php $paginaActual = 'inicio'; include '../includes/sidebar_recepcionista.php'; ?>

    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-grid-fill" style="color:var(--dorado); margin-right:8px;"></i>Inicio</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-headset me-1"></i>Recepcionista</span>
            </div>
        </div>

        <div class="content-area">

            <h1 class="page-title">Bienvenida, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Recepcionista') ?></h1>
            <p class="page-subtitle">Resumen del día — <?= date('d/m/Y') ?></p>

            <!-- Flash messages -->
            <?php if ($flashExito): ?>
            <div class="alert alert-success" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flashExito) ?>
            </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
            <div class="alert alert-danger" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flashError) ?>
            </div>
            <?php endif; ?>

            <!-- ── Tarjetas de resumen ── -->
            <div class="resumen-grid">

                <div class="resumen-card">
                    <div class="resumen-icono" style="background:#fff3cd;">
                        <i class="bi bi-calendar-day-fill" style="color:#856404;"></i>
                    </div>
                    <div class="resumen-info">
                        <span class="resumen-valor"><?= $citasHoy ?></span>
                        <span class="resumen-label">Citas hoy</span>
                    </div>
                    <a href="dashboard_recepcionista_ver_citas.php" class="resumen-link">
                        Ver <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="resumen-card">
                    <div class="resumen-icono" style="background:#f8d7da;">
                        <i class="bi bi-calendar-x-fill" style="color:#842029;"></i>
                    </div>
                    <div class="resumen-info">
                        <span class="resumen-valor"><?= $citasSinPaciente ?></span>
                        <span class="resumen-label">Citas sin paciente</span>
                    </div>
                    <a href="dashboard_recepcionista_ver_citas.php" class="resumen-link">
                        Ver <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="resumen-card">
                    <div class="resumen-icono" style="background:#e2d9f3;">
                        <i class="bi bi-person-exclamation-fill" style="color:#5a23a0;"></i>
                    </div>
                    <div class="resumen-info">
                        <span class="resumen-valor"><?= $visitantesPendientes ?></span>
                        <span class="resumen-label">Visitantes por promover</span>
                    </div>
                    <a href="dashboard_recepcionista_promover_paciente.php" class="resumen-link">
                        Gestionar <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="resumen-card">
                    <div class="resumen-icono" style="background:#d1e7dd;">
                        <i class="bi bi-people-fill" style="color:#0f5132;"></i>
                    </div>
                    <div class="resumen-info">
                        <span class="resumen-valor"><?= $totalPacientes ?></span>
                        <span class="resumen-label">Pacientes registrados</span>
                    </div>
                    <a href="dashboard_recepcionista_ver_usuarios.php" class="resumen-link">
                        Ver <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            </div>

            <!-- ── Dos columnas: citas de hoy + visitantes recientes ── -->
            <div class="dashboard-cols">

                <!-- Citas de hoy -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h3><i class="bi bi-calendar2-check-fill me-2" style="color:var(--dorado);"></i>Citas de hoy</h3>
                        <a href="dashboard_recepcionista_crear_cita.php" class="panel-header-link">
                            <i class="bi bi-plus-circle"></i> Nueva cita
                        </a>
                    </div>

                    <?php if ($resProximas && $resProximas->num_rows > 0): ?>
                    <div class="panel-list">
                        <?php while ($cita = $resProximas->fetch_assoc()): ?>
                        <div class="panel-item">
                            <div class="panel-item-hora">
                                <span><?= substr($cita['horaInicioCita'], 0, 5) ?></span>
                                <span class="panel-item-hora-fin"><?= substr($cita['horaFinalCita'], 0, 5) ?></span>
                            </div>
                            <div class="panel-item-info">
                                <span class="panel-item-titulo">
                                    <?= $cita['contPaciente'] ? 'Paciente asignado' : '<em style="color:#aaa;">Sin paciente</em>' ?>
                                </span>
                                <span class="panel-item-sub">
                                    Dr. <?= htmlspecialchars($cita['nombreDoctor']) ?>
                                    · <?= htmlspecialchars($cita['lugarCita']) ?>
                                </span>
                            </div>
                            <span class="panel-item-badge"
                                  style="<?= badgeEstado($cita['estadoCita']) ?>">
                                <?= ucfirst($cita['estadoCita']) ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="panel-vacio">
                        <i class="bi bi-calendar2-x" style="font-size:2rem; color:#ccc;"></i>
                        <p>No hay citas programadas para hoy</p>
                        <a href="dashboard_recepcionista_crear_cita.php" class="servicio-link">
                            Crear una cita <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Visitantes pendientes de promover -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h3><i class="bi bi-person-check-fill me-2" style="color:var(--dorado);"></i>Visitantes recientes</h3>
                        <a href="dashboard_recepcionista_promover_paciente.php" class="panel-header-link">
                            Ver todos <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <?php if ($resVisitantesRecientes && $resVisitantesRecientes->num_rows > 0): ?>
                    <div class="panel-list">
                        <?php while ($v = $resVisitantesRecientes->fetch_assoc()): ?>
                        <div class="panel-item">
                            <div class="resumen-icono" style="background:#f7f5ee; width:36px; height:36px; flex-shrink:0;">
                                <i class="bi bi-person-fill" style="color:#aaa; font-size:1rem;"></i>
                            </div>
                            <div class="panel-item-info">
                                <span class="panel-item-titulo">
                                    <?= htmlspecialchars($v['nameUser'] . ' ' . $v['secondNameUser']) ?>
                                </span>
                                <span class="panel-item-sub"><?= htmlspecialchars($v['emailUser']) ?></span>
                            </div>
                            <a href="dashboard_recepcionista_promover_paciente.php?buscar=1&busq_tipoId=<?= urlencode($v['tipoId']) ?>&busq_idUser=<?= urlencode($v['idUser']) ?>"
                               class="panel-item-badge"
                               style="background:#e2d9f3; color:#5a23a0; text-decoration:none; cursor:pointer;">
                                Promover
                            </a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="panel-vacio">
                        <i class="bi bi-person-check" style="font-size:2rem; color:#ccc;"></i>
                        <p>No hay visitantes pendientes de promover</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
