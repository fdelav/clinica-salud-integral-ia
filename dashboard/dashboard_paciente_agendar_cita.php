<?php
require_once '../includes/sesiones.php';
verificarRol(['paciente']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contPaciente = $usuario['id'];
$hoy = date('Y-m-d');

// ── Parámetros GET ─────────────────────────────────────────────────────────
// modo: 'doctor' | 'fecha' | null (muestra las dos opciones)
// reagendar: contCita  (si viene de "Reagendar", prellenar con la cita y fluir a buscar)
$modo      = $_GET['modo']      ?? null;
$reagendar = isset($_GET['reagendar']) ? (int)$_GET['reagendar'] : null;

// Si viene a reagendar, forzamos modo a null para que elija primero
// (pero guardamos el contCita original para usarlo después de inscribirse)
$citaOriginal = null;
if ($reagendar) {
    $stmtOrig = $conn->prepare("SELECT contCita, fechaCita, horaInicioCita FROM citas WHERE contCita = ? AND contPaciente = ?");
    $stmtOrig->bind_param("ii", $reagendar, $contPaciente);
    $stmtOrig->execute();
    $ro = $stmtOrig->get_result();
    $citaOriginal = $ro->fetch_assoc();
    $stmtOrig->close();
    // Si la cita no le pertenece, ignorar
    if (!$citaOriginal) $reagendar = null;
}

// ── Acción POST: inscribirse en una cita ──────────────────────────────────
$mensajeExito = '';
$mensajeError  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contCita'])) {
    $contCitaInscribir = (int) $_POST['contCita'];
    $reagendarOrigen   = (int) ($_POST['reagendarOrigen'] ?? 0);

    // Verificar que la cita existe, está disponible (contPaciente IS NULL) y es futura
    $stmtDisp = $conn->prepare("SELECT contCita, estadoCita, fechaCita FROM citas WHERE contCita = ? AND contPaciente IS NULL AND fechaCita >= ?");
    $stmtDisp->bind_param("is", $contCitaInscribir, $hoy);
    $stmtDisp->execute();
    $rd = $stmtDisp->get_result();
    $citaDisp = $rd->fetch_assoc();
    $stmtDisp->close();

    if (!$citaDisp) {
        $mensajeError = 'Esta cita ya no está disponible. Por favor elige otra.';
    } else {
        // Inscribirse
        $stmtInscr = $conn->prepare("UPDATE citas SET contPaciente = ?, estadoCita = 'pendiente' WHERE contCita = ?");
        $stmtInscr->bind_param("ii", $contPaciente, $contCitaInscribir);
        $stmtInscr->execute();
        $stmtInscr->close();

        // Si venía a reagendar: liberar la cita original
        if ($reagendarOrigen) {
            $stmtLib = $conn->prepare("UPDATE citas SET contPaciente = NULL, estadoCita = 'pendiente' WHERE contCita = ? AND contPaciente = ?");
            $stmtLib->bind_param("ii", $reagendarOrigen, $contPaciente);
            $stmtLib->execute();
            $stmtLib->close();
            $mensajeExito = '¡Reagendado exitosamente! Tu cita anterior quedó liberada.';
        } else {
            $mensajeExito = '¡Te has inscrito en la cita correctamente!';
        }
        $reagendar = null;
        $modo = null;
    }
}

// ── Cargar doctores (para selector) ──────────────────────────────────────
$stmtDocs = $conn->prepare("SELECT `cont`, nameUser, secondNameUser FROM usuario WHERE rolUser = 'doctor' ORDER BY nameUser ASC");
$stmtDocs->execute();
$resDocs = $stmtDocs->get_result();
$doctores = [];
while ($d = $resDocs->fetch_assoc()) $doctores[] = $d;
$stmtDocs->close();

// ── Buscar citas disponibles según modo ──────────────────────────────────
$citasDisponibles = [];
$busquedaRealizada = false;

if ($modo === 'doctor' && isset($_GET['contDoctor'])) {
    $busquedaRealizada = true;
    $contDoctor = (int) $_GET['contDoctor'];
    $stmt = $conn->prepare("
        SELECT c.contCita, c.fechaCita, c.horaInicioCita, c.horaFinalCita, c.lugarCita, c.motivoCita,
               CONCAT(u.nameUser, ' ', u.secondNameUser) AS nombreDoctor
        FROM citas c
        JOIN usuario u ON u.`cont` = c.contDoctor
        WHERE c.contDoctor = ? AND c.contPaciente IS NULL AND c.fechaCita >= ?
              AND c.estadoCita IN ('pendiente','confirmada')
        ORDER BY c.fechaCita ASC, c.horaInicioCita ASC
    ");
    $stmt->bind_param("is", $contDoctor, $hoy);
    $stmt->execute();
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) $citasDisponibles[] = $row;
    $stmt->close();
}

if ($modo === 'fecha' && isset($_GET['fechaBuscar'])) {
    $busquedaRealizada = true;
    $fechaBuscar = $_GET['fechaBuscar'];
    // Validar que no sea pasada
    if ($fechaBuscar >= $hoy) {
        $stmt = $conn->prepare("
            SELECT c.contCita, c.fechaCita, c.horaInicioCita, c.horaFinalCita, c.lugarCita, c.motivoCita,
                   CONCAT(u.nameUser, ' ', u.secondNameUser) AS nombreDoctor
            FROM citas c
            JOIN usuario u ON u.`cont` = c.contDoctor
            WHERE c.fechaCita = ? AND c.contPaciente IS NULL
                  AND c.estadoCita IN ('pendiente','confirmada')
            ORDER BY c.horaInicioCita ASC
        ");
        $stmt->bind_param("s", $fechaBuscar);
        $stmt->execute();
        $r = $stmt->get_result();
        while ($row = $r->fetch_assoc()) $citasDisponibles[] = $row;
        $stmt->close();
    } else {
        $mensajeError = 'La fecha de búsqueda no puede ser anterior a hoy.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agendar Cita — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_paciente.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'agendar_cita'; include '../includes/sidebar_paciente.php'; ?>

    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-calendar-plus-fill" style="color:var(--dorado); margin-right:8px;"></i>Agendar cita</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-person-fill me-1"></i>Paciente</span>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1><?= $reagendar ? 'Reagendar cita' : 'Agendar una cita' ?></h1>
                <p><?= $reagendar ? 'Elige una nueva cita disponible. Tu cita anterior quedará liberada.' : 'Encuentra y reserva una cita médica disponible' ?></p>
            </div>

            <!-- Mensajes flash -->
            <?php if ($mensajeExito): ?>
                <div class="pac-alert pac-alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($mensajeExito) ?>
                    <a href="dashboard_paciente_gestionar_citas.php" style="margin-left:12px; font-weight:700; color:inherit;">Ver mis citas →</a>
                </div>
            <?php endif; ?>
            <?php if ($mensajeError): ?>
                <div class="pac-alert pac-alert-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <?php if ($reagendar && $citaOriginal): ?>
                <!-- Aviso cita a reagendar -->
                <div class="pac-reagendar-aviso">
                    <i class="bi bi-arrow-repeat"></i>
                    <div>
                        <strong>Reagendando cita del <?= (new DateTime($citaOriginal['fechaCita']))->format('d/m/Y') ?> a las <?= substr($citaOriginal['horaInicioCita'],0,5) ?></strong><br>
                        <span>Elige una nueva cita y la anterior se liberará automáticamente.</span>
                    </div>
                    <a href="dashboard_paciente_gestionar_citas.php" class="pac-btn-salir ms-auto">Cancelar</a>
                </div>
            <?php endif; ?>

            <?php if (!$modo): ?>
            <!-- ══ PASO 1: Elegir modo de búsqueda ══ -->
            <h3 class="section-title"><i class="bi bi-search"></i> ¿Cómo quieres buscar?</h3>
            <div class="pac-modo-grid">
                <a href="?modo=doctor<?= $reagendar ? "&reagendar=$reagendar" : '' ?>" class="pac-modo-card">
                    <div class="pac-modo-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="pac-modo-titulo">Por doctor</div>
                    <div class="pac-modo-desc">Elige un médico específico y ve sus citas disponibles</div>
                    <div class="pac-modo-flecha"><i class="bi bi-arrow-right-circle-fill"></i></div>
                </a>
                <a href="?modo=fecha<?= $reagendar ? "&reagendar=$reagendar" : '' ?>" class="pac-modo-card">
                    <div class="pac-modo-icon"><i class="bi bi-calendar-date-fill"></i></div>
                    <div class="pac-modo-titulo">Por fecha</div>
                    <div class="pac-modo-desc">Selecciona un día y ve todos los horarios disponibles</div>
                    <div class="pac-modo-flecha"><i class="bi bi-arrow-right-circle-fill"></i></div>
                </a>
            </div>

            <?php elseif ($modo === 'doctor'): ?>
            <!-- ══ MODO DOCTOR ══ -->
            <a href="?<?= $reagendar ? "reagendar=$reagendar" : '' ?>" class="pac-volver">
                <i class="bi bi-arrow-left me-1"></i> Cambiar modo de búsqueda
            </a>

            <h3 class="section-title"><i class="bi bi-person-badge-fill"></i> Buscar por doctor</h3>

            <form method="GET" class="pac-busqueda-form">
                <input type="hidden" name="modo" value="doctor">
                <?php if ($reagendar): ?><input type="hidden" name="reagendar" value="<?= $reagendar ?>"><?php endif; ?>
                <div class="pac-busqueda-row">
                    <div class="pac-busqueda-grupo">
                        <label class="form-label" for="contDoctor">Selecciona un doctor</label>
                        <select name="contDoctor" id="contDoctor" class="form-select" required>
                            <option value="">— Elige un doctor —</option>
                            <?php foreach ($doctores as $doc): ?>
                                <option value="<?= $doc['cont'] ?>" <?= (isset($_GET['contDoctor']) && $_GET['contDoctor'] == $doc['cont']) ? 'selected' : '' ?>>
                                    Dr. <?= htmlspecialchars($doc['nameUser'] . ' ' . $doc['secondNameUser']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="pac-btn-buscar">
                        <i class="bi bi-search me-1"></i> Buscar citas
                    </button>
                </div>
            </form>

            <?php if ($busquedaRealizada): ?>
                <?php if (empty($citasDisponibles)): ?>
                    <div class="pac-empty">
                        <i class="bi bi-calendar-x"></i>
                        <p>Este doctor no tiene citas disponibles por el momento.</p>
                    </div>
                <?php else: ?>
                    <div class="pac-citas-disponibles-grid">
                    <?php foreach ($citasDisponibles as $cd):
                        $fecha = new DateTime($cd['fechaCita']);
                    ?>
                        <div class="pac-cita-disponible">
                            <div class="pac-disp-fecha">
                                <div class="pac-disp-dia"><?= $fecha->format('d') ?></div>
                                <div class="pac-disp-mes"><?= strtoupper($fecha->format('M')) ?></div>
                            </div>
                            <div class="pac-disp-info">
                                <div class="pac-disp-doctor"><i class="bi bi-person-badge-fill me-1" style="color:var(--dorado);"></i>Dr. <?= htmlspecialchars($cd['nombreDoctor']) ?></div>
                                <div class="pac-disp-hora"><i class="bi bi-clock me-1"></i><?= substr($cd['horaInicioCita'],0,5) ?> – <?= substr($cd['horaFinalCita'],0,5) ?></div>
                                <div class="pac-disp-lugar"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($cd['lugarCita']) ?></div>
                                <?php if ($cd['motivoCita']): ?>
                                    <div class="pac-disp-motivo"><i class="bi bi-card-text me-1"></i><?= htmlspecialchars($cd['motivoCita']) ?></div>
                                <?php endif; ?>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="contCita" value="<?= $cd['contCita'] ?>">
                                <input type="hidden" name="reagendarOrigen" value="<?= $reagendar ?? 0 ?>">
                                <button type="submit" class="pac-btn-inscribir">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= $reagendar ? 'Reagendar aquí' : 'Inscribirme' ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php elseif ($modo === 'fecha'): ?>
            <!-- ══ MODO FECHA ══ -->
            <a href="?<?= $reagendar ? "reagendar=$reagendar" : '' ?>" class="pac-volver">
                <i class="bi bi-arrow-left me-1"></i> Cambiar modo de búsqueda
            </a>

            <h3 class="section-title"><i class="bi bi-calendar-date-fill"></i> Buscar por fecha</h3>

            <form method="GET" class="pac-busqueda-form">
                <input type="hidden" name="modo" value="fecha">
                <?php if ($reagendar): ?><input type="hidden" name="reagendar" value="<?= $reagendar ?>"><?php endif; ?>
                <div class="pac-busqueda-row">
                    <div class="pac-busqueda-grupo">
                        <label class="form-label" for="fechaBuscar">Selecciona una fecha</label>
                        <input type="date" name="fechaBuscar" id="fechaBuscar" class="form-control"
                               min="<?= $hoy ?>"
                               value="<?= htmlspecialchars($_GET['fechaBuscar'] ?? '') ?>" required>
                    </div>
                    <button type="submit" class="pac-btn-buscar">
                        <i class="bi bi-search me-1"></i> Ver disponibilidad
                    </button>
                </div>
            </form>

            <?php if ($busquedaRealizada): ?>
                <?php if (empty($citasDisponibles)): ?>
                    <div class="pac-empty">
                        <i class="bi bi-calendar-x"></i>
                        <p>No hay citas disponibles para la fecha seleccionada.</p>
                    </div>
                <?php else: ?>
                    <div class="pac-citas-disponibles-grid">
                    <?php foreach ($citasDisponibles as $cd):
                        $fecha = new DateTime($cd['fechaCita']);
                    ?>
                        <div class="pac-cita-disponible">
                            <div class="pac-disp-fecha">
                                <div class="pac-disp-dia"><?= $fecha->format('d') ?></div>
                                <div class="pac-disp-mes"><?= strtoupper($fecha->format('M')) ?></div>
                            </div>
                            <div class="pac-disp-info">
                                <div class="pac-disp-doctor"><i class="bi bi-person-badge-fill me-1" style="color:var(--dorado);"></i>Dr. <?= htmlspecialchars($cd['nombreDoctor']) ?></div>
                                <div class="pac-disp-hora"><i class="bi bi-clock me-1"></i><?= substr($cd['horaInicioCita'],0,5) ?> – <?= substr($cd['horaFinalCita'],0,5) ?></div>
                                <div class="pac-disp-lugar"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($cd['lugarCita']) ?></div>
                                <?php if ($cd['motivoCita']): ?>
                                    <div class="pac-disp-motivo"><i class="bi bi-card-text me-1"></i><?= htmlspecialchars($cd['motivoCita']) ?></div>
                                <?php endif; ?>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="contCita" value="<?= $cd['contCita'] ?>">
                                <input type="hidden" name="reagendarOrigen" value="<?= $reagendar ?? 0 ?>">
                                <button type="submit" class="pac-btn-inscribir">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    <?= $reagendar ? 'Reagendar aquí' : 'Inscribirme' ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>

</body>
</html>
