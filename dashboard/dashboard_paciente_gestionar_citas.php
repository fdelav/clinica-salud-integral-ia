<?php
require_once '../includes/sesiones.php';
verificarRol(['paciente']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contPaciente = $usuario['id'];
$hoy = date('Y-m-d');

// ── Acción: salir de una cita (desuscribirse) ──────────────────────────────
$mensajeExito = '';
$mensajeError  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    $contCita = (int) ($_POST['contCita'] ?? 0);

    // Verificar que la cita pertenece a este paciente y es futura
    $stmtCheck = $conn->prepare("SELECT contCita, estadoCita, fechaCita FROM citas WHERE contCita = ? AND contPaciente = ?");
    $stmtCheck->bind_param("ii", $contCita, $contPaciente);
    $stmtCheck->execute();
    $resCk = $stmtCheck->get_result();
    $citaRow = $resCk->fetch_assoc();
    $stmtCheck->close();

    if (!$citaRow) {
        $mensajeError = 'Cita no encontrada o no te pertenece.';
    } elseif ($citaRow['fechaCita'] < $hoy) {
        $mensajeError = 'No puedes modificar una cita pasada.';
    } elseif ($citaRow['estadoCita'] === 'cancelada') {
        $mensajeError = 'Esta cita ya está cancelada.';
    } else {
        if ($_POST['accion'] === 'salir') {
            // Desuscribirse: quitar el paciente (dejar disponible para otro)
            $stmtSalir = $conn->prepare("UPDATE citas SET contPaciente = NULL, estadoCita = 'pendiente' WHERE contCita = ?");
            $stmtSalir->bind_param("i", $contCita);
            $stmtSalir->execute();
            $stmtSalir->close();
            $mensajeExito = 'Te has dado de baja de la cita correctamente.';
        }
    }
}

// ── Filtro activo ─────────────────────────────────────────────────────────
$filtro = $_GET['filtro'] ?? 'proximas';
$filtros = [
    'proximas'   => ['label' => 'Próximas',   'icon' => 'bi-calendar-event-fill'],
    'completadas'=> ['label' => 'Completadas','icon' => 'bi-check-circle-fill'],
    'canceladas' => ['label' => 'Canceladas', 'icon' => 'bi-x-circle-fill'],
    'todas'      => ['label' => 'Todas',      'icon' => 'bi-list-ul'],
];
if (!array_key_exists($filtro, $filtros)) $filtro = 'proximas';

// ── Consulta según filtro ─────────────────────────────────────────────────
$whereExtra = match($filtro) {
    'proximas'    => "AND c.estadoCita IN ('pendiente','confirmada') AND c.fechaCita >= '$hoy'",
    'completadas' => "AND c.estadoCita = 'completada'",
    'canceladas'  => "AND c.estadoCita = 'cancelada'",
    default       => "",
};

$stmtCitas = $conn->prepare("
    SELECT c.contCita, c.fechaCita, c.horaInicioCita, c.horaFinalCita,
           c.lugarCita, c.motivoCita, c.estadoCita,
           CONCAT(u.nameUser, ' ', u.secondNameUser) AS nombreDoctor
    FROM citas c
    JOIN usuario u ON u.`cont` = c.contDoctor
    WHERE c.contPaciente = ? $whereExtra
    ORDER BY c.fechaCita DESC, c.horaInicioCita DESC
");
$stmtCitas->bind_param("i", $contPaciente);
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
    <link href="../Css/dashboard_paciente.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'gestionar_citas'; include '../includes/sidebar_paciente.php'; ?>

    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-calendar2-week-fill" style="color:var(--dorado); margin-right:8px;"></i>Mis Citas</h2>
            <div class="topbar-right">
                <a href="dashboard_paciente_agendar_cita.php" class="pac-btn-agendar">
                    <i class="bi bi-plus-lg me-1"></i> Agendar cita
                </a>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1>Gestión de citas</h1>
                <p>Revisa, cancela o reagenda tus citas médicas</p>
            </div>

            <!-- Mensajes flash -->
            <?php if ($mensajeExito): ?>
                <div class="pac-alert pac-alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($mensajeExito) ?></div>
            <?php endif; ?>
            <?php if ($mensajeError): ?>
                <div class="pac-alert pac-alert-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <!-- Filtros de estado -->
            <div class="pac-filtros">
                <?php foreach ($filtros as $key => $f): ?>
                    <a href="?filtro=<?= $key ?>" class="pac-filtro-btn <?= $filtro === $key ? 'active' : '' ?>">
                        <i class="bi <?= $f['icon'] ?>"></i> <?= $f['label'] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Listado de citas -->
            <?php if ($resCitas->num_rows === 0): ?>
                <div class="pac-empty">
                    <i class="bi bi-calendar-x"></i>
                    <p>No tienes citas en esta categoría.</p>
                    <a href="dashboard_paciente_agendar_cita.php" class="pac-btn-agendar mt-3">
                        <i class="bi bi-plus-lg me-1"></i> Agendar una cita
                    </a>
                </div>
            <?php else: ?>
                <div class="pac-citas-grid">
                <?php while ($c = $resCitas->fetch_assoc()):
                    $fecha = new DateTime($c['fechaCita']);
                    $esFutura = $c['fechaCita'] >= $hoy;
                    $puedeAccion = $esFutura && in_array($c['estadoCita'], ['pendiente', 'confirmada']);

                    $estadoStyle = match($c['estadoCita']) {
                        'confirmada'  => 'background:#d1fae5;color:#065f46;',
                        'pendiente'   => 'background:#fef9c3;color:#854d0e;',
                        'cancelada'   => 'background:#fee2e2;color:#991b1b;',
                        'completada'  => 'background:#e0e7ff;color:#3730a3;',
                        default       => '',
                    };
                ?>
                    <div class="pac-cita-card <?= !$esFutura ? 'pac-cita-pasada' : '' ?>">
                        <div class="pac-cita-fecha-col">
                            <div class="pac-cita-dia"><?= $fecha->format('d') ?></div>
                            <div class="pac-cita-mes"><?= strtoupper($fecha->format('M')) ?></div>
                            <div class="pac-cita-anio"><?= $fecha->format('Y') ?></div>
                        </div>
                        <div class="pac-cita-body">
                            <div class="pac-cita-doctor">
                                <i class="bi bi-person-badge-fill me-1" style="color:var(--dorado);"></i>
                                Dr. <?= htmlspecialchars($c['nombreDoctor']) ?>
                            </div>
                            <div class="pac-cita-hora">
                                <i class="bi bi-clock me-1"></i>
                                <?= substr($c['horaInicioCita'],0,5) ?> – <?= substr($c['horaFinalCita'],0,5) ?>
                            </div>
                            <div class="pac-cita-lugar">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                <?= htmlspecialchars($c['lugarCita']) ?>
                            </div>
                            <?php if ($c['motivoCita']): ?>
                                <div class="pac-cita-motivo">
                                    <i class="bi bi-card-text me-1"></i>
                                    <?= htmlspecialchars($c['motivoCita']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="pac-cita-acciones">
                            <span class="panel-item-badge" style="<?= $estadoStyle ?>"><?= ucfirst($c['estadoCita']) ?></span>
                            <?php if ($puedeAccion): ?>
                                <a href="dashboard_paciente_agendar_cita.php?reagendar=<?= $c['contCita'] ?>" class="pac-btn-reagendar">
                                    <i class="bi bi-arrow-repeat me-1"></i>Reagendar
                                </a>
                                <form method="POST" onsubmit="return confirm('¿Seguro que quieres darte de baja de esta cita?');">
                                    <input type="hidden" name="accion" value="salir">
                                    <input type="hidden" name="contCita" value="<?= $c['contCita'] ?>">
                                    <button type="submit" class="pac-btn-salir">
                                        <i class="bi bi-calendar-x me-1"></i>Salir
                                    </button>
                                </form>
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
