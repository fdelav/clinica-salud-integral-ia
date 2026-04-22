<?php
require_once '../includes/sesiones.php';
verificarRol(['doctor']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contDoctor = $usuario['id'];

$mensajeExito = '';
$mensajeError  = '';

// ── Pre-cargar datos si viene desde una cita ──────────────────────────────
$citaPreloaded = null;
$contCitaGET   = isset($_GET['contCita']) ? (int)$_GET['contCita'] : null;
if ($contCitaGET) {
    $s = $conn->prepare("
        SELECT c.contCita, c.fechaCita, c.motivoCita,
               c.contPaciente,
               CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente
        FROM citas c
        JOIN usuario u ON u.`cont` = c.contPaciente
        WHERE c.contCita = ? AND c.contDoctor = ? AND c.contPaciente IS NOT NULL
    ");
    $s->bind_param("ii", $contCitaGET, $contDoctor);
    $s->execute();
    $citaPreloaded = $s->get_result()->fetch_assoc();
    $s->close();
}

// ── POST: guardar historia ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contPaciente       = (int)   ($_POST['contPaciente']      ?? 0);
    $contCita           = !empty($_POST['contCita'])    ? (int)$_POST['contCita']    : null;
    $fechaExpedicion    =          $_POST['fechaExpedicion']   ?? date('Y-m-d');
    $motivoConsulta     = trim(    $_POST['motivoConsulta']    ?? '');
    $sintomas           = trim(    $_POST['sintomas']          ?? '') ?: null;
    $diagnostico        = trim(    $_POST['diagnostico']       ?? '') ?: null;
    $recetaMedica       = trim(    $_POST['recetaMedica']      ?? '') ?: null;
    $incapacidadMedica  = trim(    $_POST['incapacidadMedica'] ?? '') ?: null;

    if (!$contPaciente || !$motivoConsulta || !$fechaExpedicion) {
        $mensajeError = 'Paciente, fecha y motivo de consulta son obligatorios.';
    } else {
        // Verificar que el paciente existe
        $sP = $conn->prepare("SELECT `cont` FROM usuario WHERE `cont`=? AND rolUser='paciente'");
        $sP->bind_param("i", $contPaciente); $sP->execute(); $sP->store_result();
        if ($sP->num_rows === 0) {
            $mensajeError = 'El paciente seleccionado no existe.';
        } else {
            $sP->close();
            $sIns = $conn->prepare("
                INSERT INTO historias_medicas
                    (contDoctor, contPaciente, contCita, fechaExpedicion,
                     motivoConsulta, sintomas, diagnostico, recetaMedica, incapacidadMedica)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $sIns->bind_param(
                "iiissssss",
                $contDoctor, $contPaciente, $contCita, $fechaExpedicion,
                $motivoConsulta, $sintomas, $diagnostico, $recetaMedica, $incapacidadMedica
            );
            // bind correcto sin espacio
            $sIns->close();

            // Re-preparar limpio
            $stmt2 = $conn->prepare("
                INSERT INTO historias_medicas
                    (contDoctor, contPaciente, contCita, fechaExpedicion,
                     motivoConsulta, sintomas, diagnostico, recetaMedica, incapacidadMedica)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt2->bind_param("iiissssss",
                $contDoctor, $contPaciente, $contCita, $fechaExpedicion,
                $motivoConsulta, $sintomas, $diagnostico, $recetaMedica, $incapacidadMedica
            );
            if ($stmt2->execute()) {
                $mensajeExito = 'Historia médica creada exitosamente.';
                // Limpiar preload para formulario vacío
                $citaPreloaded = null;
                $contCitaGET = null;
            } else {
                $mensajeError = 'Error al guardar la historia: ' . $conn->error;
            }
            $stmt2->close();
        }
    }
}

// ── Cargar pacientes para selector ───────────────────────────────────────
$sP2 = $conn->prepare("
    SELECT u.`cont`, u.nameUser, u.secondNameUser, u.idUser, u.tipoId
    FROM usuario u
    WHERE u.rolUser = 'paciente'
    ORDER BY u.nameUser ASC
");
$sP2->execute();
$resPacientes = $sP2->get_result();
$pacientes = [];
while ($p = $resPacientes->fetch_assoc()) $pacientes[] = $p;
$sP2->close();

// ── Cargar citas del doctor con paciente asignado (para selector de cita) ─
$sCitas = $conn->prepare("
    SELECT c.contCita, c.fechaCita, c.horaInicioCita,
           CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente,
           c.contPaciente
    FROM citas c
    JOIN usuario u ON u.`cont` = c.contPaciente
    WHERE c.contDoctor = ? AND c.contPaciente IS NOT NULL
    ORDER BY c.fechaCita DESC, c.horaInicioCita DESC
    LIMIT 50
");
$sCitas->bind_param("i", $contDoctor);
$sCitas->execute();
$resCitasSelect = $sCitas->get_result();
$citasSelect = [];
while ($cs = $resCitasSelect->fetch_assoc()) $citasSelect[] = $cs;
$sCitas->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva Historia Médica — Clínica Salud Integral</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_doctor.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'crear_historia'; include '../includes/sidebar_doctor.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-file-earmark-plus-fill" style="color:var(--dorado); margin-right:8px;"></i>Nueva Historia Médica</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-person-badge-fill me-1"></i>Doctor</span>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1>Crear historia médica</h1>
                <p>Registra la información clínica de la consulta</p>
            </div>

            <?php if ($mensajeExito): ?>
                <div class="pac-alert pac-alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($mensajeExito) ?>
                    <a href="dashboard_doctor_historias.php" style="margin-left:12px;font-weight:700;color:inherit;">Ver historias →</a>
                </div>
            <?php endif; ?>
            <?php if ($mensajeError): ?>
                <div class="pac-alert pac-alert-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <?php if ($citaPreloaded): ?>
                <div class="doc-cita-preload-aviso">
                    <i class="bi bi-calendar-check-fill"></i>
                    <div>
                        <strong>Historia vinculada a cita del <?= (new DateTime($citaPreloaded['fechaCita']))->format('d/m/Y') ?></strong><br>
                        Paciente: <?= htmlspecialchars($citaPreloaded['nombrePaciente']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="doc-historia-form">
                <?php if ($citaPreloaded): ?>
                    <input type="hidden" name="contCita"    value="<?= $citaPreloaded['contCita'] ?>">
                    <input type="hidden" name="contPaciente" value="<?= $citaPreloaded['contPaciente'] ?>">
                <?php endif; ?>

                <!-- Sección: Datos generales -->
                <div class="doc-form-section">
                    <div class="doc-form-section-title">
                        <i class="bi bi-person-vcard-fill"></i> Datos generales
                    </div>
                    <div class="doc-form-grid">

                        <!-- Paciente -->
                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="contPaciente">
                                Paciente <span class="doc-requerido">*</span>
                            </label>
                            <?php if ($citaPreloaded): ?>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($citaPreloaded['nombrePaciente']) ?>" disabled>
                            <?php else: ?>
                                <select name="contPaciente" id="contPaciente" class="form-select" required>
                                    <option value="">— Selecciona un paciente —</option>
                                    <?php foreach ($pacientes as $p): ?>
                                        <option value="<?= $p['cont'] ?>"
                                            <?= (isset($_POST['contPaciente']) && $_POST['contPaciente'] == $p['cont']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nameUser'] . ' ' . $p['secondNameUser']) ?>
                                            (<?= strtoupper($p['tipoId']) ?>: <?= htmlspecialchars($p['idUser']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Fecha de expedición -->
                        <div class="doc-form-grupo">
                            <label class="form-label" for="fechaExpedicion">
                                Fecha de expedición <span class="doc-requerido">*</span>
                            </label>
                            <input type="date" name="fechaExpedicion" id="fechaExpedicion" class="form-control"
                                   value="<?= htmlspecialchars($_POST['fechaExpedicion'] ?? date('Y-m-d')) ?>"
                                   max="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Cita asociada (opcional) -->
                        <?php if (!$citaPreloaded): ?>
                        <div class="doc-form-grupo">
                            <label class="form-label" for="contCita">
                                Cita asociada <span class="doc-opcional">(opcional)</span>
                            </label>
                            <select name="contCita" id="contCita" class="form-select">
                                <option value="">— Sin cita asociada —</option>
                                <?php foreach ($citasSelect as $cs): ?>
                                    <option value="<?= $cs['contCita'] ?>"
                                        <?= (isset($_POST['contCita']) && $_POST['contCita'] == $cs['contCita']) ? 'selected' : '' ?>>
                                        <?= (new DateTime($cs['fechaCita']))->format('d/m/Y') ?>
                                        <?= substr($cs['horaInicioCita'],0,5) ?> —
                                        <?= htmlspecialchars($cs['nombrePaciente']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Sección: Motivo -->
                <div class="doc-form-section">
                    <div class="doc-form-section-title">
                        <i class="bi bi-chat-square-text-fill"></i> Consulta
                    </div>
                    <div class="doc-form-grid">

                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="motivoConsulta">
                                Motivo de consulta <span class="doc-requerido">*</span>
                            </label>
                            <input type="text" name="motivoConsulta" id="motivoConsulta" class="form-control"
                                   placeholder="Ej: Dolor de cabeza persistente, control anual…"
                                   value="<?= htmlspecialchars($_POST['motivoConsulta'] ?? $citaPreloaded['motivoCita'] ?? '') ?>"
                                   maxlength="255" required>
                        </div>

                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="sintomas">
                                Síntomas <span class="doc-opcional">(opcional)</span>
                            </label>
                            <textarea name="sintomas" id="sintomas" class="form-control doc-textarea"
                                      placeholder="Describe los síntomas reportados por el paciente…"
                                      rows="3"><?= htmlspecialchars($_POST['sintomas'] ?? '') ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- Sección: Diagnóstico y tratamiento -->
                <div class="doc-form-section">
                    <div class="doc-form-section-title">
                        <i class="bi bi-clipboard2-pulse-fill"></i> Diagnóstico y tratamiento
                    </div>
                    <div class="doc-form-grid">

                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="diagnostico">
                                Diagnóstico <span class="doc-opcional">(opcional)</span>
                            </label>
                            <textarea name="diagnostico" id="diagnostico" class="form-control doc-textarea"
                                      placeholder="Diagnóstico médico…"
                                      rows="3"><?= htmlspecialchars($_POST['diagnostico'] ?? '') ?></textarea>
                        </div>

                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="recetaMedica">
                                Receta médica <span class="doc-opcional">(opcional)</span>
                            </label>
                            <textarea name="recetaMedica" id="recetaMedica" class="form-control doc-textarea"
                                      placeholder="Medicamentos, dosis, frecuencia…"
                                      rows="3"><?= htmlspecialchars($_POST['recetaMedica'] ?? '') ?></textarea>
                        </div>

                        <div class="doc-form-grupo col-full">
                            <label class="form-label" for="incapacidadMedica">
                                Incapacidad médica <span class="doc-opcional">(opcional)</span>
                            </label>
                            <input type="text" name="incapacidadMedica" id="incapacidadMedica" class="form-control"
                                   placeholder="Ej: Reposo 3 días, incapacidad laboral parcial…"
                                   value="<?= htmlspecialchars($_POST['incapacidadMedica'] ?? '') ?>"
                                   maxlength="255">
                        </div>

                    </div>
                </div>

                <!-- Acciones -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary-clinic" style="max-width:220px;">
                        <i class="bi bi-save-fill me-2"></i>Guardar historia
                    </button>
                    <a href="dashboard_doctor_historias.php" class="btn-secondary-clinic">Cancelar</a>
                </div>

            </form>

        </div>
    </main>

</body>
</html>
