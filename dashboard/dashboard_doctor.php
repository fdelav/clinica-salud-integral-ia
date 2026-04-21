<?php
require_once '../includes/sesiones.php';
verificarRol(['doctor']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contDoctor = $usuario['id'];
$hoy = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Doctor — Clínica Salud Integral</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_doctor.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'inicio'; include '../includes/sidebar_doctor.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-grid-fill" style="color:var(--dorado); margin-right:8px;"></i>Inicio</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-person-badge-fill me-1"></i>Doctor</span>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1>Bienvenido, Dr. <?= htmlspecialchars($usuario['nombre'] ?? '') ?> 👨‍⚕️</h1>
                <p>Panel médico — Clínica Salud Integral</p>
            </div>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <?php
                // Citas hoy
                $s = $conn->prepare("SELECT COUNT(*) FROM citas WHERE contDoctor=? AND fechaCita=? AND estadoCita IN ('pendiente','confirmada')");
                $s->bind_param("is", $contDoctor, $hoy); $s->execute(); $s->bind_result($citasHoy); $s->fetch(); $s->close();

                // Citas esta semana
                $finSemana = date('Y-m-d', strtotime('+6 days'));
                $s = $conn->prepare("SELECT COUNT(*) FROM citas WHERE contDoctor=? AND fechaCita BETWEEN ? AND ? AND estadoCita IN ('pendiente','confirmada')");
                $s->bind_param("iss", $contDoctor, $hoy, $finSemana); $s->execute(); $s->bind_result($citasSemana); $s->fetch(); $s->close();

                // Total historias
                $s = $conn->prepare("SELECT COUNT(*) FROM historias_medicas WHERE contDoctor=?");
                $s->bind_param("i", $contDoctor); $s->execute(); $s->bind_result($totalHistorias); $s->fetch(); $s->close();

                // Pacientes atendidos (distintos)
                $s = $conn->prepare("SELECT COUNT(DISTINCT contPaciente) FROM historias_medicas WHERE contDoctor=?");
                $s->bind_param("i", $contDoctor); $s->execute(); $s->bind_result($pacientesAtendidos); $s->fetch(); $s->close();
                ?>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar-day-fill"></i></div>
                    <div class="stat-value"><?= $citasHoy ?></div>
                    <div class="stat-label">Citas hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar-week-fill"></i></div>
                    <div class="stat-value"><?= $citasSemana ?></div>
                    <div class="stat-label">Citas esta semana</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-journal-medical"></i></div>
                    <div class="stat-value"><?= $totalHistorias ?></div>
                    <div class="stat-label">Historias médicas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-value"><?= $pacientesAtendidos ?></div>
                    <div class="stat-label">Pacientes atendidos</div>
                </div>
            </div>

            <!-- Columnas -->
            <div class="dashboard-cols">

                <!-- Citas de hoy -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h3><i class="bi bi-calendar-day me-2" style="color:var(--dorado);"></i>Citas de hoy</h3>
                        <a href="dashboard_doctor_citas.php" class="panel-header-link">Ver todas <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="panel-list">
                    <?php
                    $s = $conn->prepare("
                        SELECT c.horaInicioCita, c.horaFinalCita, c.lugarCita, c.motivoCita, c.estadoCita,
                               CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente
                        FROM citas c
                        LEFT JOIN usuario u ON u.`cont` = c.contPaciente
                        WHERE c.contDoctor=? AND c.fechaCita=?
                        ORDER BY c.horaInicioCita ASC
                    ");
                    $s->bind_param("is", $contDoctor, $hoy);
                    $s->execute();
                    $rHoy = $s->get_result();
                    if ($rHoy->num_rows === 0):
                    ?>
                        <div class="panel-vacio">
                            <i class="bi bi-calendar-x" style="font-size:2rem;color:#ddd;"></i>
                            <p>Sin citas para hoy.</p>
                        </div>
                    <?php else: while ($c = $rHoy->fetch_assoc()):
                        $badgeStyle = $c['estadoCita'] === 'confirmada'
                            ? 'background:#d1fae5;color:#065f46;'
                            : 'background:#fef9c3;color:#854d0e;';
                    ?>
                        <div class="panel-item">
                            <div class="panel-item-hora">
                                <?= substr($c['horaInicioCita'],0,5) ?>
                                <span class="panel-item-hora-fin"><?= substr($c['horaFinalCita'],0,5) ?></span>
                            </div>
                            <div class="panel-item-info">
                                <div class="panel-item-titulo">
                                    <?= $c['nombrePaciente'] ? htmlspecialchars($c['nombrePaciente']) : '<em style="color:#aaa;">Sin paciente</em>' ?>
                                </div>
                                <div class="panel-item-sub">
                                    <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($c['lugarCita']) ?>
                                    <?php if ($c['motivoCita']): ?> · <?= htmlspecialchars($c['motivoCita']) ?><?php endif; ?>
                                </div>
                            </div>
                            <span class="panel-item-badge" style="<?= $badgeStyle ?>"><?= ucfirst($c['estadoCita']) ?></span>
                        </div>
                    <?php endwhile; endif; $s->close(); ?>
                    </div>
                </div>

                <!-- Últimas historias -->
                <div class="dashboard-panel">
                    <div class="panel-header">
                        <h3><i class="bi bi-journal-medical me-2" style="color:var(--dorado);"></i>Últimas historias</h3>
                        <a href="dashboard_doctor_historias.php" class="panel-header-link">Ver todas <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="panel-list">
                    <?php
                    $s = $conn->prepare("
                        SELECT h.countHistoria, h.fechaExpedicion, h.motivoConsulta,
                               CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente
                        FROM historias_medicas h
                        JOIN usuario u ON u.`cont` = h.contPaciente
                        WHERE h.contDoctor=?
                        ORDER BY h.fechaExpedicion DESC, h.countHistoria DESC
                        LIMIT 5
                    ");
                    $s->bind_param("i", $contDoctor);
                    $s->execute();
                    $rHist = $s->get_result();
                    if ($rHist->num_rows === 0):
                    ?>
                        <div class="panel-vacio">
                            <i class="bi bi-journal-x" style="font-size:2rem;color:#ddd;"></i>
                            <p>Aún no has creado historias médicas.</p>
                        </div>
                    <?php else: while ($h = $rHist->fetch_assoc()):
                        $fecha = new DateTime($h['fechaExpedicion']);
                    ?>
                        <div class="panel-item">
                            <div class="panel-item-hora">
                                <?= $fecha->format('d/m') ?>
                                <span class="panel-item-hora-fin"><?= $fecha->format('Y') ?></span>
                            </div>
                            <div class="panel-item-info">
                                <div class="panel-item-titulo"><?= htmlspecialchars($h['nombrePaciente']) ?></div>
                                <div class="panel-item-sub"><?= htmlspecialchars($h['motivoConsulta']) ?></div>
                            </div>
                            <a href="dashboard_doctor_historias.php?editar=<?= $h['countHistoria'] ?>" class="doc-btn-sm">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </div>
                    <?php endwhile; endif; $s->close(); ?>
                    </div>
                </div>

            </div>

            <!-- Accesos rápidos -->
            <h3 class="section-title" style="margin-top:28px;"><i class="bi bi-lightning-fill"></i> Accesos rápidos</h3>
            <div class="quick-grid">
                <a href="dashboard_doctor_citas.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-calendar2-check-fill"></i></span>
                    <span class="qc-label">Mis citas</span>
                </a>
                <a href="dashboard_doctor_crear_historia.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-file-earmark-plus-fill"></i></span>
                    <span class="qc-label">Nueva historia</span>
                </a>
                <a href="dashboard_doctor_historias.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-journal-medical"></i></span>
                    <span class="qc-label">Ver historias</span>
                </a>
                <a href="dashboard_doctor_crear_historia.php" class="quick-card">
                    <span class="qc-icon"><i class="bi bi-person-lines-fill"></i></span>
                    <span class="qc-label">Historial paciente</span>
                </a>
            </div>

        </div>
    </main>

</body>
</html>
