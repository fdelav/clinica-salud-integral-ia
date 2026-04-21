<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// Obtener lista de doctores
$resDoctores = $conn->query(
    "SELECT cont, nameUser, secondNameUser FROM usuario WHERE rolUser = 'doctor' ORDER BY nameUser"
);

$conn->close();

// Flash error si viene de un intento fallido (conservar datos del form)
$flashError  = $_SESSION['recep_error']     ?? null;
$datosForm   = $_SESSION['recep_form_data'] ?? [];
unset($_SESSION['recep_error'], $_SESSION['recep_form_data']);

// Helper para repoblar campos
function old(string $campo, array $datos, string $default = ''): string {
    return htmlspecialchars($datos[$campo] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Cita — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css"  rel="stylesheet">

    <style>
        .form-card {
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 18px;
            padding: 32px 28px;
            max-width: 720px;
        }
        .form-section-label {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: var(--dorado);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--amarillo-borde);
        }
        .form-label {
            font-size: 0.83rem;
            font-weight: 700;
            color: #3a2f00;
            margin-bottom: 5px;
            display: block;
            font-family: "Montserrat", sans-serif;
        }
        .form-label .req { color: #e05252; margin-left: 2px; }

        /* Separador entre secciones del form */
        .form-sep {
            border: none;
            border-top: 1.5px dashed var(--amarillo-borde);
            margin: 24px 0;
        }

        /* Info box paciente */
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fffdf0;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.85rem;
            color: #7a6000;
            margin-bottom: 8px;
        }
        .info-box i { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
    </style>
</head>
<body>
    <?php $paginaActual = 'crear_cita'; include '../includes/sidebar_recepcionista.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-calendar-plus-fill" style="color:var(--dorado); margin-right:8px;"></i>Nueva cita</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-headset"></i> Recepcionista</span>
            </div>
        </div>

        <div class="content-area">

            <h1 class="page-title">Crear nueva cita</h1>
            <p class="page-subtitle">Completa los datos para registrar la cita. El paciente se asignará después.</p>

            <!-- Flash error -->
            <?php if ($flashError): ?>
            <div style="background:#f8d7da; border:1.5px solid #f1aeb5; border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#842029; font-weight:600; max-width:720px;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($flashError) ?>
            </div>
            <?php endif; ?>

            <div class="form-card">
                <form method="POST" action="../Php/crearCita.php">

                    <!-- ── Sección: Fecha y horario ── -->
                    <div class="form-section-label">
                        <i class="bi bi-clock"></i> Fecha y horario
                    </div>

                    <div class="form-grid">
                        <div class="col-full">
                            <label class="form-label" for="fechaCita">
                                Fecha <span class="req">*</span>
                            </label>
                            <input type="date" id="fechaCita" name="fechaCita"
                                   class="form-control"
                                   value="<?= old('fechaCita', $datosForm) ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                        </div>
                        <div>
                            <label class="form-label" for="horaInicioCita">
                                Hora de inicio <span class="req">*</span>
                            </label>
                            <input type="time" id="horaInicioCita" name="horaInicioCita"
                                   class="form-control"
                                   value="<?= old('horaInicioCita', $datosForm) ?>"
                                   required>
                        </div>
                        <div>
                            <label class="form-label" for="horaFinalCita">
                                Hora de fin <span class="req">*</span>
                            </label>
                            <input type="time" id="horaFinalCita" name="horaFinalCita"
                                   class="form-control"
                                   value="<?= old('horaFinalCita', $datosForm) ?>"
                                   required>
                        </div>
                    </div>

                    <hr class="form-sep">

                    <!-- ── Sección: Doctor y lugar ── -->
                    <div class="form-section-label">
                        <i class="bi bi-person-badge"></i> Doctor y lugar
                    </div>

                    <div class="form-grid">
                        <div class="col-full">
                            <label class="form-label" for="contDoctor">
                                Doctor <span class="req">*</span>
                            </label>
                            <select id="contDoctor" name="contDoctor" class="form-select" required>
                                <option value="">— Selecciona un doctor —</option>
                                <?php while ($doc = $resDoctores->fetch_assoc()): ?>
                                <option value="<?= $doc['cont'] ?>"
                                    <?= old('contDoctor', $datosForm) == $doc['cont'] ? 'selected' : '' ?>>
                                    Dr. <?= htmlspecialchars($doc['nameUser'].' '.$doc['secondNameUser']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-full">
                            <label class="form-label" for="lugarCita">
                                Lugar / consultorio <span class="req">*</span>
                            </label>
                            <input type="text" id="lugarCita" name="lugarCita"
                                   class="form-control"
                                   placeholder="Ej: Consultorio 3 — Piso 2"
                                   maxlength="150"
                                   value="<?= old('lugarCita', $datosForm) ?>"
                                   required>
                        </div>
                    </div>

                    <hr class="form-sep">

                    <!-- ── Sección: Motivo ── -->
                    <div class="form-section-label">
                        <i class="bi bi-card-text"></i> Motivo
                    </div>

                    <div>
                        <label class="form-label" for="motivoCita">
                            Motivo de la cita <span style="color:#aaa; font-weight:400;">(opcional)</span>
                        </label>
                        <textarea id="motivoCita" name="motivoCita"
                                  class="form-control"
                                  rows="3"
                                  maxlength="255"
                                  placeholder="Describe brevemente el motivo de la cita..."
                                  style="resize:vertical;"><?= old('motivoCita', $datosForm) ?></textarea>
                    </div>

                    <hr class="form-sep">

                    <!-- ── Sección: Paciente ── -->
                    <div class="form-section-label">
                        <i class="bi bi-person"></i> Paciente
                    </div>

                    <div class="info-box">
                        <i class="bi bi-info-circle-fill" style="color:var(--dorado);"></i>
                        <span>
                            La cita se creará <strong>sin paciente asignado</strong>. Una vez creada,
                            podrás asignar al paciente desde la sección
                            <a href="dashboard_recepcionista_ver_citas.php" style="color:var(--dorado); font-weight:700;">Ver citas</a>.
                        </span>
                    </div>

                    <!-- ── Acciones ── -->
                    <div class="form-actions" style="margin-top:28px;">
                        <button type="submit" class="btn-primary-clinic" style="width:auto; padding:12px 36px;">
                            <i class="bi bi-calendar-check me-2"></i>Crear cita
                        </button>
                        <a href="dashboard_recepcionista_ver_citas.php" class="btn-secondary-clinic">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>

        </div><!-- /content-area -->
    </main>
</body>
</html>
