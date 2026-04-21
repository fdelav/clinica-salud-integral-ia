<?php
require_once '../includes/sesiones.php';
verificarRol(['doctor']);
$usuario = obtenerUsuario();
require_once '../Php/coneccion.php';
$contDoctor = $usuario['id'];

$mensajeExito = '';
$mensajeError  = '';

// ── POST: guardar edición ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['countHistoria'])) {
    $countHistoria      = (int)   $_POST['countHistoria'];
    $fechaExpedicion    =          $_POST['fechaExpedicion']   ?? '';
    $motivoConsulta     = trim(    $_POST['motivoConsulta']    ?? '');
    $sintomas           = trim(    $_POST['sintomas']          ?? '') ?: null;
    $diagnostico        = trim(    $_POST['diagnostico']       ?? '') ?: null;
    $recetaMedica       = trim(    $_POST['recetaMedica']      ?? '') ?: null;
    $incapacidadMedica  = trim(    $_POST['incapacidadMedica'] ?? '') ?: null;

    if (!$motivoConsulta || !$fechaExpedicion) {
        $mensajeError = 'La fecha y el motivo de consulta son obligatorios.';
    } else {
        // Verificar que la historia pertenece a este doctor
        $sCheck = $conn->prepare("SELECT countHistoria FROM historias_medicas WHERE countHistoria=? AND contDoctor=?");
        $sCheck->bind_param("ii", $countHistoria, $contDoctor);
        $sCheck->execute();
        $sCheck->store_result();
        if ($sCheck->num_rows === 0) {
            $mensajeError = 'Historia no encontrada o no te pertenece.';
        } else {
            $sCheck->close();
            $sUp = $conn->prepare("
                UPDATE historias_medicas SET
                    fechaExpedicion=?, motivoConsulta=?, sintomas=?,
                    diagnostico=?, recetaMedica=?, incapacidadMedica=?
                WHERE countHistoria=? AND contDoctor=?
            ");
            $sUp->bind_param("ssssssii",
                $fechaExpedicion, $motivoConsulta, $sintomas,
                $diagnostico, $recetaMedica, $incapacidadMedica,
                $countHistoria, $contDoctor
            );
            if ($sUp->execute()) {
                $mensajeExito = 'Historia médica actualizada correctamente.';
            } else {
                $mensajeError = 'Error al actualizar: ' . $conn->error;
            }
            $sUp->close();
        }
    }
}

// ── GET: cargar historia para editar ─────────────────────────────────────
$historiaEditar = null;
$editarId = isset($_GET['editar']) ? (int)$_GET['editar'] : null;
// Si POST falló con error, mantener el id para mostrar form
if (isset($_POST['countHistoria']) && $mensajeError) $editarId = (int)$_POST['countHistoria'];

if ($editarId) {
    $sEd = $conn->prepare("
        SELECT h.*, CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente,
               u.idUser, u.tipoId
        FROM historias_medicas h
        JOIN usuario u ON u.`cont` = h.contPaciente
        WHERE h.countHistoria=? AND h.contDoctor=?
    ");
    $sEd->bind_param("ii", $editarId, $contDoctor);
    $sEd->execute();
    $historiaEditar = $sEd->get_result()->fetch_assoc();
    $sEd->close();
    if (!$historiaEditar) $editarId = null;
}

// ── Filtro de búsqueda ────────────────────────────────────────────────────
$busqueda = trim($_GET['q'] ?? '');

// ── Listado de historias ─────────────────────────────────────────────────
$whereBusq = '';
$paramsBusq = [$contDoctor];
$typesBusq  = 'i';

if ($busqueda) {
    $whereBusq = "AND (CONCAT(u.nameUser,' ',u.secondNameUser) LIKE ? OR h.motivoConsulta LIKE ? OR h.diagnostico LIKE ?)";
    $like = "%$busqueda%";
    $paramsBusq[] = $like;
    $paramsBusq[] = $like;
    $paramsBusq[] = $like;
    $typesBusq .= 'sss';
}

$sLista = $conn->prepare("
    SELECT h.countHistoria, h.fechaExpedicion, h.motivoConsulta,
           h.diagnostico, h.contCita,
           CONCAT(u.nameUser,' ',u.secondNameUser) AS nombrePaciente,
           u.idUser, u.tipoId
    FROM historias_medicas h
    JOIN usuario u ON u.`cont` = h.contPaciente
    WHERE h.contDoctor=? $whereBusq
    ORDER BY h.fechaExpedicion DESC, h.countHistoria DESC
");
$sLista->bind_param($typesBusq, ...$paramsBusq);
$sLista->execute();
$resLista = $sLista->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historias Médicas — Clínica Salud Integral</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/dashboard_doctor.css" rel="stylesheet">
</head>
<body>

    <?php $paginaActual = 'historias'; include '../includes/sidebar_doctor.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-journal-medical" style="color:var(--dorado); margin-right:8px;"></i>Historias Médicas</h2>
            <div class="topbar-right">
                <a href="dashboard_doctor_crear_historia.php" class="pac-btn-agendar">
                    <i class="bi bi-plus-lg me-1"></i> Nueva historia
                </a>
            </div>
        </div>

        <div class="content-area">

            <div class="welcome-header">
                <h1>Historias médicas</h1>
                <p>Consulta y edita los registros clínicos de tus pacientes</p>
            </div>

            <!-- Mensajes -->
            <?php if ($mensajeExito): ?>
                <div class="pac-alert pac-alert-success"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($mensajeExito) ?></div>
            <?php endif; ?>
            <?php if ($mensajeError): ?>
                <div class="pac-alert pac-alert-error"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($mensajeError) ?></div>
            <?php endif; ?>

            <!-- Layout: lista + panel edición -->
            <div class="doc-historias-layout <?= $historiaEditar ? 'doc-con-edicion' : '' ?>">

                <!-- ══ COLUMNA LISTA ══ -->
                <div class="doc-historias-col-lista">

                    <!-- Buscador -->
                    <form method="GET" class="doc-buscador">
                        <div class="input-icon-wrap" style="flex:1;">
                            <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;pointer-events:none;"></i>
                            <input type="text" name="q" class="form-control" style="padding-left:38px;"
                                   placeholder="Buscar por paciente, motivo o diagnóstico…"
                                   value="<?= htmlspecialchars($busqueda) ?>">
                        </div>
                        <button type="submit" class="pac-btn-buscar">Buscar</button>
                        <?php if ($busqueda): ?>
                            <a href="dashboard_doctor_historias.php" class="btn-secondary-clinic" style="padding:9px 16px;">
                                <i class="bi bi-x"></i>
                            </a>
                        <?php endif; ?>
                    </form>

                    <!-- Listado -->
                    <?php if ($resLista->num_rows === 0): ?>
                        <div class="doc-empty">
                            <i class="bi bi-journal-x"></i>
                            <p><?= $busqueda ? 'Sin resultados para "'.htmlspecialchars($busqueda).'".' : 'No has creado historias médicas aún.' ?></p>
                            <?php if (!$busqueda): ?>
                                <a href="dashboard_doctor_crear_historia.php" class="pac-btn-agendar">
                                    <i class="bi bi-plus-lg me-1"></i> Crear primera historia
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="doc-historias-lista">
                        <?php while ($h = $resLista->fetch_assoc()):
                            $fecha = new DateTime($h['fechaExpedicion']);
                            $esActiva = $editarId && $editarId == $h['countHistoria'];
                        ?>
                            <a href="?editar=<?= $h['countHistoria'] ?><?= $busqueda ? '&q='.urlencode($busqueda) : '' ?>"
                               class="doc-historia-item <?= $esActiva ? 'doc-historia-activa' : '' ?>">
                                <div class="doc-hist-fecha-col">
                                    <div class="doc-hist-dia"><?= $fecha->format('d') ?></div>
                                    <div class="doc-hist-mes"><?= strtoupper($fecha->format('M')) ?></div>
                                    <div class="doc-hist-anio"><?= $fecha->format('Y') ?></div>
                                </div>
                                <div class="doc-hist-info">
                                    <div class="doc-hist-paciente">
                                        <i class="bi bi-person-fill me-1" style="color:var(--dorado);"></i>
                                        <?= htmlspecialchars($h['nombrePaciente']) ?>
                                        <span class="doc-hist-id"><?= strtoupper($h['tipoId']) ?>: <?= htmlspecialchars($h['idUser']) ?></span>
                                    </div>
                                    <div class="doc-hist-motivo"><?= htmlspecialchars($h['motivoConsulta']) ?></div>
                                    <?php if ($h['diagnostico']): ?>
                                        <div class="doc-hist-diagnostico">
                                            <i class="bi bi-clipboard2-pulse me-1"></i><?= htmlspecialchars(mb_strimwidth($h['diagnostico'], 0, 80, '…')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($h['contCita']): ?>
                                        <span class="doc-hist-tag"><i class="bi bi-calendar-check me-1"></i>Vinculada a cita</span>
                                    <?php endif; ?>
                                </div>
                                <i class="bi bi-chevron-right doc-hist-chevron"></i>
                            </a>
                        <?php endwhile; ?>
                        </div>
                    <?php endif; $sLista->close(); ?>
                </div>

                <!-- ══ COLUMNA EDICIÓN ══ -->
                <?php if ($historiaEditar): ?>
                <div class="doc-historias-col-edicion">
                    <div class="doc-edicion-header">
                        <div>
                            <h3><i class="bi bi-pencil-square me-2" style="color:var(--dorado);"></i>Editando historia</h3>
                            <p class="doc-edicion-paciente">
                                <i class="bi bi-person-fill me-1"></i>
                                <?= htmlspecialchars($historiaEditar['nombrePaciente']) ?>
                                <span class="doc-hist-id"><?= strtoupper($historiaEditar['tipoId']) ?>: <?= htmlspecialchars($historiaEditar['idUser']) ?></span>
                            </p>
                        </div>
                        <a href="dashboard_doctor_historias.php<?= $busqueda ? '?q='.urlencode($busqueda) : '' ?>"
                           class="doc-btn-cerrar-edicion" title="Cerrar edición">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>

                    <form method="POST" class="doc-historia-form doc-edicion-form">
                        <input type="hidden" name="countHistoria" value="<?= $historiaEditar['countHistoria'] ?>">

                        <div class="doc-form-section">
                            <div class="doc-form-section-title">
                                <i class="bi bi-person-vcard-fill"></i> Datos generales
                            </div>
                            <div class="doc-form-grid">
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label">Paciente</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($historiaEditar['nombrePaciente']) ?>" disabled>
                                </div>
                                <div class="doc-form-grupo">
                                    <label class="form-label" for="fechaExpedicionEd">
                                        Fecha de expedición <span class="doc-requerido">*</span>
                                    </label>
                                    <input type="date" name="fechaExpedicion" id="fechaExpedicionEd" class="form-control"
                                           value="<?= htmlspecialchars($historiaEditar['fechaExpedicion']) ?>"
                                           max="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="doc-form-section">
                            <div class="doc-form-section-title">
                                <i class="bi bi-chat-square-text-fill"></i> Consulta
                            </div>
                            <div class="doc-form-grid">
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label" for="motivoConsultaEd">
                                        Motivo de consulta <span class="doc-requerido">*</span>
                                    </label>
                                    <input type="text" name="motivoConsulta" id="motivoConsultaEd" class="form-control"
                                           value="<?= htmlspecialchars($historiaEditar['motivoConsulta']) ?>" maxlength="255" required>
                                </div>
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label" for="sintomasEd">
                                        Síntomas <span class="doc-opcional">(opcional)</span>
                                    </label>
                                    <textarea name="sintomas" id="sintomasEd" class="form-control doc-textarea"
                                              rows="3"><?= htmlspecialchars($historiaEditar['sintomas'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="doc-form-section">
                            <div class="doc-form-section-title">
                                <i class="bi bi-clipboard2-pulse-fill"></i> Diagnóstico y tratamiento
                            </div>
                            <div class="doc-form-grid">
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label" for="diagnosticoEd">
                                        Diagnóstico <span class="doc-opcional">(opcional)</span>
                                    </label>
                                    <textarea name="diagnostico" id="diagnosticoEd" class="form-control doc-textarea"
                                              rows="3"><?= htmlspecialchars($historiaEditar['diagnostico'] ?? '') ?></textarea>
                                </div>
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label" for="recetaEd">
                                        Receta médica <span class="doc-opcional">(opcional)</span>
                                    </label>
                                    <textarea name="recetaMedica" id="recetaEd" class="form-control doc-textarea"
                                              rows="3"><?= htmlspecialchars($historiaEditar['recetaMedica'] ?? '') ?></textarea>
                                </div>
                                <div class="doc-form-grupo col-full">
                                    <label class="form-label" for="incapacidadEd">
                                        Incapacidad médica <span class="doc-opcional">(opcional)</span>
                                    </label>
                                    <input type="text" name="incapacidadMedica" id="incapacidadEd" class="form-control"
                                           value="<?= htmlspecialchars($historiaEditar['incapacidadMedica'] ?? '') ?>"
                                           maxlength="255">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary-clinic" style="max-width:200px;">
                                <i class="bi bi-save-fill me-2"></i>Guardar cambios
                            </button>
                            <a href="dashboard_doctor_historias.php<?= $busqueda ? '?q='.urlencode($busqueda) : '' ?>"
                               class="btn-secondary-clinic">Cancelar</a>
                        </div>

                    </form>
                </div>
                <?php else: ?>
                <!-- Placeholder cuando no hay nada seleccionado (layout de dos columnas) -->
                <div class="doc-historias-col-edicion doc-edicion-placeholder">
                    <div class="doc-placeholder-inner">
                        <i class="bi bi-arrow-left-circle" style="font-size:2.5rem;color:#ddd;"></i>
                        <p>Selecciona una historia de la lista para editarla</p>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /doc-historias-layout -->

        </div>
    </main>

</body>
</html>
