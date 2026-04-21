<?php
require_once '../includes/sesiones.php';
verificarRol(['doctor']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contDoctor = $usuario['id'];
$hoy = date('Y-m-d');

// Filtro de fecha
$filtro = $_GET['filtro'] ?? 'proximas';
$filtros = [
    'proximas'   => ['label' => 'Próximas',    'icon' => 'bi-calendar-event-fill'],
    'hoy'        => ['label' => 'Hoy',          'icon' => 'bi-calendar-day-fill'],
    'completadas'=> ['label' => 'Completadas',  'icon' => 'bi-check-circle-fill'],
    'todas'      => ['label' => 'Todas',        'icon' => 'bi-list-ul'],
];
if (!array_key_exists($filtro, $filtros)) $filtro = 'proximas';

$whereExtra = match($filtro) {
    'proximas'    => "AND c.fechaCita >= '$hoy' AND c.estadoCita IN ('pendiente','confirmada')",
    'hoy'         => "AND c.fechaCita = '$hoy'",
    'completadas' => "AND c.estadoCita = 'completada'",
    default       => "",
};

$stmtCitas = $conn->prepare("
    SELECT c.contCita, c.fechaCita, c.horaInicioCita, c.horaFinalCita,
           c.lugarCita, c.motivoCita, c.estadoCita,
           CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente,
           u.emailUser, u.telUser
    FROM citas c
    LEFT JOIN usuario u ON u.`cont` = c.contPaciente
    WHERE c.contDoctor = ? $whereExtra
    ORDER BY c.fechaCita ASC, c.horaInicioCita ASC
");
$stmtCitas->bind_param("i", $contDoctor);
$stmtCitas->execute();
$resCitas = $stmtCitas->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Citas — Clínica Salud Integral</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_doctor.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'citas'; include '../includes/sidebar_doctor.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-calendar2-check-fill" style="color:var(--dorado); margin-right:8px;"></i>Mis Citas</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-person-badge-fill me-1"></i>Doctor</span>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1>Agenda médica</h1>
                <p>Tus citas asignadas, ordenadas por fecha</p>
            </div>

            <!-- Filtros -->
            <div class="pac-filtros">
                <?php foreach ($filtros as $key => $f): ?>
                    <a href="?filtro=<?= $key ?>" class="pac-filtro-btn <?= $filtro === $key ? 'active' : '' ?>">
                        <i class="bi <?= $f['icon'] ?>"></i> <?= $f['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tabla de citas -->
            <?php if ($resCitas->num_rows === 0): ?>
                <div class="doc-empty">
                    <i class="bi bi-calendar-x"></i>
                    <p>No hay citas en esta categoría.</p>
                </div>
            <?php else:
                $fechaActual = null;
            ?>
                <div class="doc-citas-lista">
                <?php while ($c = $resCitas->fetch_assoc()):
                    $fecha = new DateTime($c['fechaCita']);
                    $fechaStr = $c['fechaCita'];
                    $esHoy = $fechaStr === $hoy;

                    // Separador de fecha
                    if ($fechaStr !== $fechaActual):
                        $fechaActual = $fechaStr;
                ?>
                        <div class="doc-fecha-sep <?= $esHoy ? 'doc-fecha-hoy' : '' ?>">
                            <span>
                                <?php if ($esHoy): ?>
                                    <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> HOY —
                                <?php endif; ?>
                                <?= $fecha->format('l, d \d\e F \d\e Y') ?>
                            </span>
                        </div>
                <?php endif; ?>

                    <?php
                    $badgeStyle = match($c['estadoCita']) {
                        'confirmada'  => 'background:#d1fae5;color:#065f46;',
                        'pendiente'   => 'background:#fef9c3;color:#854d0e;',
                        'cancelada'   => 'background:#fee2e2;color:#991b1b;',
                        'completada'  => 'background:#e0e7ff;color:#3730a3;',
                        default       => '',
                    };
                    $tienePaciente = !empty($c['nombrePaciente']);
                    ?>
                    <div class="doc-cita-row <?= !$tienePaciente ? 'doc-cita-libre' : '' ?>">
                        <!-- Hora -->
                        <div class="doc-cita-hora">
                            <span class="doc-hora-inicio"><?= substr($c['horaInicioCita'],0,5) ?></span>
                            <span class="doc-hora-sep">|</span>
                            <span class="doc-hora-fin"><?= substr($c['horaFinalCita'],0,5) ?></span>
                        </div>

                        <!-- Info paciente -->
                        <div class="doc-cita-paciente">
                            <?php if ($tienePaciente): ?>
                                <div class="doc-paciente-nombre">
                                    <i class="bi bi-person-fill me-1" style="color:var(--dorado);"></i>
                                    <?= htmlspecialchars($c['nombrePaciente']) ?>
                                </div>
                                <div class="doc-paciente-contacto">
                                    <?php if ($c['emailUser']): ?>
                                        <span><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($c['emailUser']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($c['telUser']): ?>
                                        <span><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['telUser']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="doc-paciente-nombre" style="color:#bbb; font-style:italic;">
                                    <i class="bi bi-person-dash me-1"></i>Sin paciente asignado
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Lugar y motivo -->
                        <div class="doc-cita-detalle">
                            <span><i class="bi bi-geo-alt-fill me-1" style="color:var(--dorado);"></i><?= htmlspecialchars($c['lugarCita']) ?></span>
                            <?php if ($c['motivoCita']): ?>
                                <span><i class="bi bi-card-text me-1"></i><?= htmlspecialchars($c['motivoCita']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Estado + acción -->
                        <div class="doc-cita-acciones">
                            <span class="panel-item-badge" style="<?= $badgeStyle ?>"><?= ucfirst($c['estadoCita']) ?></span>
                            <?php if ($tienePaciente): ?>
                                <a href="dashboard_doctor_crear_historia.php?contCita=<?= $c['contCita'] ?>"
                                   class="doc-btn-historia" title="Crear historia médica para esta cita">
                                    <i class="bi bi-file-earmark-plus me-1"></i>Historia
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endwhile; ?>
                </div>
            <?php endif; $stmtCitas->close(); ?>

        </div>
    </main>

</body>
</html>
