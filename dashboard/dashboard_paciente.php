<?php
require_once '../includes/sesiones.php';
verificarRol(['paciente']);
$usuario = obtenerUsuario();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Panel — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_paciente.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'inicio'; include '../includes/sidebar_paciente.php'; ?>

    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-grid-fill" style="color:var(--dorado); margin-right:8px;"></i>Inicio</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-person-fill me-1"></i>Paciente</span>
            </div>
        </div>

        <!-- Área de contenido -->
        <div class="content-area">

            <div class="welcome-header">
                <h1>Bienvenido, <?= htmlspecialchars($usuario['nombre'] ?? 'Paciente') ?> 👋</h1>
                <p>Este es tu panel personal — Clínica Salud Integral</p>
            </div>

            <!-- Resumen rápido -->
            <div class="stats-grid" style="margin-bottom:32px;">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <?php
                    require_once '../Php/coneccion.php';
                    $contPaciente = $usuario['id'];

                    // Citas próximas (pendientes o confirmadas, fecha >= hoy)
                    $hoy = date('Y-m-d');
                    $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM citas WHERE contPaciente = ? AND estadoCita IN ('pendiente','confirmada') AND fechaCita >= ?");
                    $stmtTotal->bind_param("is", $contPaciente, $hoy);
                    $stmtTotal->execute();
                    $stmtTotal->bind_result($totalProximas);
                    $stmtTotal->fetch();
                    $stmtTotal->close();
                    ?>
                    <div class="stat-value"><?= $totalProximas ?></div>
                    <div class="stat-label">Citas próximas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <?php
                    $stmtComp = $conn->prepare("SELECT COUNT(*) FROM citas WHERE contPaciente = ? AND estadoCita = 'completada'");
                    $stmtComp->bind_param("i", $contPaciente);
                    $stmtComp->execute();
                    $stmtComp->bind_result($totalCompletadas);
                    $stmtComp->fetch();
                    $stmtComp->close();
                    ?>
                    <div class="stat-value"><?= $totalCompletadas ?></div>
                    <div class="stat-label">Citas completadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                    <?php
                    // Cita más próxima
                    $stmtProx = $conn->prepare("
                        SELECT fechaCita FROM citas
                        WHERE contPaciente = ? AND estadoCita IN ('pendiente','confirmada') AND fechaCita >= ?
                        ORDER BY fechaCita ASC, horaInicioCita ASC LIMIT 1
                    ");
                    $stmtProx->bind_param("is", $contPaciente, $hoy);
                    $stmtProx->execute();
                    $stmtProx->bind_result($fechaProxima);
                    $stmtProx->fetch();
                    $stmtProx->close();

                    if ($fechaProxima) {
                        $diff = (new DateTime($fechaProxima))->diff(new DateTime($hoy))->days;
                        $valorProx = $diff === 0 ? 'Hoy' : "En $diff d.";
                    } else {
                        $valorProx = '—';
                    }
                    ?>
                    <div class="stat-value" style="font-size:1.3rem;"><?= $valorProx ?></div>
                    <div class="stat-label">Próxima cita</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-x-circle-fill" style="color:#e05252;"></i></div>
                    <?php
                    $stmtCanc = $conn->prepare("SELECT COUNT(*) FROM citas WHERE contPaciente = ? AND estadoCita = 'cancelada'");
                    $stmtCanc->bind_param("i", $contPaciente);
                    $stmtCanc->execute();
                    $stmtCanc->bind_result($totalCanceladas);
                    $stmtCanc->fetch();
                    $stmtCanc->close();
                    ?>
                    <div class="stat-value"><?= $totalCanceladas ?></div>
                    <div class="stat-label">Canceladas</div>
                </div>
            </div>

            <!-- Mis próximas citas -->
            <h3 class="section-title"><i class="bi bi-calendar-event-fill"></i> Mis próximas citas</h3>

            <div class="dashboard-panel" style="margin-bottom:28px;">
                <div class="panel-header">
                    <h3><i class="bi bi-calendar2-week me-2" style="color:var(--dorado);"></i>Citas programadas</h3>
                    <a href="dashboard_paciente_gestionar_citas.php" class="panel-header-link">
                        Ver todas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="panel-list">
                <?php
                $stmtCitas = $conn->prepare("
                    SELECT c.fechaCita, c.horaInicioCita, c.horaFinalCita, c.lugarCita,
                           c.motivoCita, c.estadoCita,
                           CONCAT(u.nameUser, ' ', u.secondNameUser) AS nombreDoctor
                    FROM citas c
                    JOIN usuario u ON u.`cont` = c.contDoctor
                    WHERE c.contPaciente = ? AND c.fechaCita >= ? AND c.estadoCita IN ('pendiente','confirmada')
                    ORDER BY c.fechaCita ASC, c.horaInicioCita ASC
                    LIMIT 5
                ");
                $stmtCitas->bind_param("is", $contPaciente, $hoy);
                $stmtCitas->execute();
                $resCitas = $stmtCitas->get_result();

                if ($resCitas->num_rows === 0):
                ?>
                    <div class="panel-vacio">
                        <i class="bi bi-calendar-x" style="font-size:2rem; color:#ddd;"></i>
                        <p>No tienes citas próximas agendadas.</p>
                        <a href="dashboard_paciente_agendar_cita.php" class="panel-header-link mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Agendar una cita
                        </a>
                    </div>
                <?php else: while ($cita = $resCitas->fetch_assoc()): 
                    $fecha = new DateTime($cita['fechaCita']);
                    $badgeClass = $cita['estadoCita'] === 'confirmada' 
                        ? 'background:#d1fae5;color:#065f46;' 
                        : 'background:#fef9c3;color:#854d0e;';
                ?>
                    <div class="panel-item">
                        <div class="panel-item-hora">
                            <?= $fecha->format('d/m') ?>
                            <span class="panel-item-hora-fin"><?= substr($cita['horaInicioCita'],0,5) ?></span>
                        </div>
                        <div class="panel-item-info">
                            <div class="panel-item-titulo">
                                Dr. <?= htmlspecialchars($cita['nombreDoctor']) ?>
                            </div>
                            <div class="panel-item-sub">
                                <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($cita['lugarCita']) ?>
                                <?php if ($cita['motivoCita']): ?> · <?= htmlspecialchars($cita['motivoCita']) ?><?php endif; ?>
                            </div>
                        </div>
                        <span class="panel-item-badge" style="<?= $badgeClass ?>">
                            <?= ucfirst($cita['estadoCita']) ?>
                        </span>
                    </div>
                <?php endwhile; endif; $stmtCitas->close(); ?>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <h3 class="section-title"><i class="bi bi-lightning-fill"></i> Accesos rápidos</h3>
            <div class="quick-grid">
                <a href="dashboard_paciente_agendar_cita.php?modo=doctor" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-person-badge-fill"></i></span>
                    <span class="qc-label">Buscar por doctor</span>
                </a>
                <a href="dashboard_paciente_agendar_cita.php?modo=fecha" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-calendar-date-fill"></i></span>
                    <span class="qc-label">Buscar por fecha</span>
                </a>
                <a href="dashboard_paciente_gestionar_citas.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-calendar2-week-fill"></i></span>
                    <span class="qc-label">Ver mis citas</span>
                </a>
                <a href="dashboard_paciente_agendar_cita.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-calendar-plus-fill"></i></span>
                    <span class="qc-label">Agendar cita</span>
                </a>
            </div>

        </div>
    </main>

</body>
</html>
