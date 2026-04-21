<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Filtros de la tabla ──────────────────────────────────────────────────────
$filtroFecha    = $_GET['fecha']    ?? '';
$filtroEstado   = $_GET['estado']   ?? '';
$filtroDoctor   = $_GET['doctor']   ?? '';
$filtroPaciente = trim($_GET['paciente'] ?? '');

// ── Panel asignar paciente ───────────────────────────────────────────────────
// ?asignar=contCita  abre el panel para esa cita
// ?asignar=contCita&busq_pac=texto  además muestra resultados de búsqueda
$citaAsignar     = isset($_GET['asignar']) ? (int)$_GET['asignar'] : 0;
$busqPac         = trim($_GET['busq_pac'] ?? '');
$resultadosBusq  = [];
$infoCitaAsignar = null;

if ($citaAsignar > 0) {
    $stmtCA = $conn->prepare(
        "SELECT c.contCita, c.fechaCita, c.horaInicioCita,
                CONCAT(d.nameUser,' ',d.secondNameUser) AS nombreDoctor
         FROM citas c
         LEFT JOIN usuario d ON d.cont = c.contDoctor
         WHERE c.contCita = ? AND c.estadoCita NOT IN ('cancelada','completada')"
    );
    $stmtCA->bind_param('i', $citaAsignar);
    $stmtCA->execute();
    $resCA = $stmtCA->get_result();
    $infoCitaAsignar = $resCA->num_rows ? $resCA->fetch_assoc() : null;

    if ($busqPac !== '' && $infoCitaAsignar) {
        $like = '%' . $busqPac . '%';
        $stmtBP = $conn->prepare(
            "SELECT cont, nameUser, secondNameUser, idUser, tipoId
             FROM usuario
             WHERE rolUser = 'paciente'
               AND (CONCAT(nameUser,' ',secondNameUser) LIKE ? OR idUser LIKE ?)
             ORDER BY nameUser, secondNameUser
             LIMIT 10"
        );
        $stmtBP->bind_param('ss', $like, $like);
        $stmtBP->execute();
        $resultadosBusq = $stmtBP->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// ── Query principal con filtros ──────────────────────────────────────────────
$where  = ['1=1'];
$params = [];
$types  = '';

if ($filtroFecha !== '') {
    $where[]  = 'c.fechaCita = ?';
    $params[] = $filtroFecha;
    $types   .= 's';
}
if ($filtroEstado !== '') {
    $where[]  = 'c.estadoCita = ?';
    $params[] = $filtroEstado;
    $types   .= 's';
}
if ($filtroDoctor !== '') {
    $where[]  = 'c.contDoctor = ?';
    $params[] = (int)$filtroDoctor;
    $types   .= 'i';
}
if ($filtroPaciente !== '') {
    $where[]  = "(CONCAT(p.nameUser,' ',p.secondNameUser) LIKE ? OR p.idUser LIKE ?)";
    $params[] = "%$filtroPaciente%";
    $params[] = "%$filtroPaciente%";
    $types   .= 'ss';
}

$whereSQL = implode(' AND ', $where);

$sql = "SELECT
            c.contCita, c.fechaCita, c.horaInicioCita, c.horaFinalCita,
            c.lugarCita, c.motivoCita, c.estadoCita,
            c.contPaciente, c.contDoctor,
            CONCAT(d.nameUser,' ',d.secondNameUser) AS nombreDoctor,
            CONCAT(p.nameUser,' ',p.secondNameUser) AS nombrePaciente,
            p.idUser AS idPaciente, p.tipoId AS tipoIdPaciente
        FROM citas c
        LEFT JOIN usuario d ON d.cont = c.contDoctor
        LEFT JOIN usuario p ON p.cont  = c.contPaciente
        WHERE $whereSQL
        ORDER BY c.fechaCita DESC, c.horaInicioCita ASC";

$stmt = $conn->prepare($sql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$resCitas = $stmt->get_result();

// Doctores para el filtro
$resDoctores = $conn->query(
    "SELECT cont, nameUser, secondNameUser FROM usuario WHERE rolUser = 'doctor' ORDER BY nameUser"
);

// Flash messages
$flashExito = $_SESSION['recep_exito'] ?? null;
$flashError = $_SESSION['recep_error'] ?? null;
unset($_SESSION['recep_exito'], $_SESSION['recep_error']);

$conn->close();

function badgeEstilo(string $estado): string {
    return match($estado) {
        'pendiente'  => 'background:#fff3cd; color:#856404;',
        'confirmada' => 'background:#d1e7dd; color:#0f5132;',
        'cancelada'  => 'background:#f8d7da; color:#842029;',
        'completada' => 'background:#cfe2ff; color:#084298;',
        default      => 'background:#e2e3e5; color:#41464b;',
    };
}

// Query string base (sin asignar/busq_pac) para preservar filtros en los enlaces
$qsFiltros = http_build_query(array_filter([
    'fecha'    => $filtroFecha,
    'estado'   => $filtroEstado,
    'doctor'   => $filtroDoctor,
    'paciente' => $filtroPaciente,
]));
$urlBase = 'dashboard_recepcionista_ver_citas.php' . ($qsFiltros ? '?'.$qsFiltros : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Citas — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css"  rel="stylesheet">

    <style>
        /* ── Filtros ── */
        .filtros-wrap {
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
        }
        .filtros-titulo {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--dorado);
            margin-bottom: 14px;
        }
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
            gap: 12px;
            align-items: end;
        }
        .filtros-grid .form-label { font-size: 0.8rem; margin-bottom: 4px; display: block; }
        .filtros-btns { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn-filtrar {
            background: var(--amarillo);
            border: none;
            border-radius: 20px;
            padding: 9px 20px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.18s;
            white-space: nowrap;
        }
        .btn-filtrar:hover { background: var(--amarillo-hover); }

        .btn-limpiar {
            background: none;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 20px;
            padding: 8px 18px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #7a6000;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
            display: inline-block;
        }
        .btn-limpiar:hover { background: #FFF6BB; }

        /* ── Panel asignar paciente ── */
        .panel-asignar {
            background: #fff;
            border: 2px solid var(--amarillo);
            border-radius: 16px;
            padding: 22px 24px;
            margin-bottom: 20px;
        }
        .panel-asignar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .panel-asignar-header h3 { font-size: 1rem; color: #3a2f00; margin: 0; }

        .panel-asignar-info {
            font-size: 0.85rem;
            color: #777;
            background: #fffdf0;
            border: 1px solid var(--amarillo-borde);
            border-radius: 10px;
            padding: 8px 14px;
            margin-bottom: 16px;
        }
        .panel-asignar-busq {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .panel-asignar-busq input { flex: 1; min-width: 180px; }

        /* Resultados de búsqueda de paciente */
        .pac-lista { display: flex; flex-direction: column; gap: 8px; }
        .pac-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 16px;
            border-radius: 12px;
            border: 1.5px solid #eee;
        }
        .pac-item-avatar {
            width: 36px; height: 36px;
            background: var(--amarillo-claro);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: var(--dorado);
            flex-shrink: 0;
            border: 1.5px solid var(--amarillo-borde);
        }
        .pac-item-info { flex: 1; }
        .pac-item-nombre { font-weight: 700; font-size: 0.88rem; color: #3a2f00; }
        .pac-item-id { font-size: 0.78rem; color: #888; }

        .btn-asignar-pac {
            background: var(--amarillo);
            border: none;
            border-radius: 20px;
            padding: 7px 16px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: background 0.18s;
            white-space: nowrap;
        }
        .btn-asignar-pac:hover { background: var(--amarillo-hover); }

        .pac-vacio {
            text-align: center;
            padding: 24px;
            color: #aaa;
            font-size: 0.9rem;
        }

        /* ── Tabla ── */
        .tabla-wrap {
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 18px;
            overflow: hidden;
        }
        .tabla-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid var(--amarillo-borde);
            background: #fffdf0;
            flex-wrap: wrap;
            gap: 10px;
        }
        .tabla-header h3 { font-size: 1rem; color: #3a2f00; margin: 0; }
        .tabla-count { font-size: 0.82rem; color: #888; }

        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        thead th {
            background: #fffdf0;
            color: #5a4800;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 14px;
            border-bottom: 1.5px solid var(--amarillo-borde);
            white-space: nowrap;
        }
        tbody tr { border-bottom: 1px solid #f5f0e0; transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fffdf0; }
        tbody tr.fila-activa { background: #fffde7; }
        td { padding: 11px 14px; color: #333; vertical-align: middle; }

        .td-hora {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #3a2f00;
            white-space: nowrap;
        }
        .td-hora small { display: block; font-weight: 400; color: #aaa; font-size: 0.75rem; }

        .badge-estado {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .acciones { display: flex; gap: 6px; flex-wrap: wrap; }
        .btn-accion {
            border: none;
            border-radius: 10px;
            padding: 5px 10px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: opacity 0.15s, transform 0.15s;
            font-family: "Nunito", sans-serif;
        }
        .btn-accion:hover { opacity: 0.82; transform: translateY(-1px); }
        .btn-asignar  { background: #e2d9f3; color: #5a23a0; }
        .btn-estado   { background: var(--amarillo-claro); color: #7a6000; border: 1px solid var(--amarillo-borde); }
        .btn-cancelar { background: #f8d7da; color: #842029; }

        /* ── Modal cambiar estado ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 500;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .modal-head {
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--amarillo-borde);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-head h3 { font-size: 1rem; color: #3a2f00; margin: 0; }
        .modal-close {
            background: none; border: none; font-size: 1.3rem;
            color: #aaa; cursor: pointer; padding: 0; line-height: 1;
        }
        .modal-close:hover { color: #555; }
        .modal-body { padding: 18px 22px; }
        .modal-foot {
            padding: 12px 22px 18px;
            border-top: 1px solid var(--amarillo-borde);
            display: flex; gap: 10px; justify-content: flex-end;
        }

        /* Radios de estado como tarjetas */
        .estado-opciones { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
        .estado-radio { display: none; }
        .estado-label {
            display: block;
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            transition: border-color 0.15s, background 0.15s;
        }
        .estado-label:hover { border-color: var(--amarillo); background: var(--amarillo-claro); }
        .estado-radio:checked + .estado-label { border-color: var(--amarillo); background: var(--amarillo-claro); }

        /* Tabla vacía */
        .tabla-vacia {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 10px; padding: 60px 20px;
            color: #bbb; text-align: center;
        }
        .tabla-vacia i { font-size: 2.4rem; }
        .tabla-vacia p { margin: 0; font-size: 0.95rem; }

        @media (max-width: 768px) {
            table { display: block; overflow-x: auto; }
            .acciones { flex-direction: column; }
        }
    </style>
</head>
<body>
    <?php $paginaActual = 'ver_citas'; include '../includes/sidebar_recepcionista.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-calendar2-week-fill" style="color:var(--dorado); margin-right:8px;"></i>Citas</h2>
            <div class="topbar-right">
                <a href="dashboard_recepcionista_crear_cita.php"
                   class="btn-accion btn-estado" style="padding:8px 16px; border-radius:20px; font-size:0.85rem;">
                    <i class="bi bi-plus-circle"></i> Nueva cita
                </a>
                <span class="topbar-badge"><i class="bi bi-headset"></i> Recepcionista</span>
            </div>
        </div>

        <div class="content-area">

            <h1 class="page-title">Ver citas</h1>
            <p class="page-subtitle">Busca, filtra y gestiona todas las citas de la clínica.</p>

            <!-- Flash messages -->
            <?php if ($flashExito): ?>
            <div style="background:#d1e7dd; border:1.5px solid #a3cfbb; border-radius:12px; padding:12px 18px; margin-bottom:18px; color:#0f5132; font-weight:600;">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flashExito) ?>
            </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
            <div style="background:#f8d7da; border:1.5px solid #f1aeb5; border-radius:12px; padding:12px 18px; margin-bottom:18px; color:#842029; font-weight:600;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($flashError) ?>
            </div>
            <?php endif; ?>

            <!-- ══ Filtros ══ -->
            <div class="filtros-wrap">
                <div class="filtros-titulo"><i class="bi bi-funnel-fill me-1"></i>Filtrar citas</div>
                <form method="GET" action="dashboard_recepcionista_ver_citas.php">
                    <div class="filtros-grid">
                        <div>
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control"
                                   value="<?= htmlspecialchars($filtroFecha) ?>">
                        </div>
                        <div>
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos</option>
                                <option value="pendiente"  <?= $filtroEstado==='pendiente'  ? 'selected':'' ?>>Pendiente</option>
                                <option value="confirmada" <?= $filtroEstado==='confirmada' ? 'selected':'' ?>>Confirmada</option>
                                <option value="completada" <?= $filtroEstado==='completada' ? 'selected':'' ?>>Completada</option>
                                <option value="cancelada"  <?= $filtroEstado==='cancelada'  ? 'selected':'' ?>>Cancelada</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Doctor</label>
                            <select name="doctor" class="form-select">
                                <option value="">Todos</option>
                                <?php while ($doc = $resDoctores->fetch_assoc()): ?>
                                <option value="<?= $doc['cont'] ?>"
                                    <?= (int)$filtroDoctor===(int)$doc['cont'] ? 'selected':'' ?>>
                                    <?= htmlspecialchars($doc['nameUser'].' '.$doc['secondNameUser']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Paciente (nombre o ID)</label>
                            <input type="text" name="paciente" class="form-control"
                                   placeholder="Ej: María o 12345678"
                                   value="<?= htmlspecialchars($filtroPaciente) ?>">
                        </div>
                        <div class="filtros-btns">
                            <button type="submit" class="btn-filtrar">
                                <i class="bi bi-search me-1"></i>Buscar
                            </button>
                            <a href="dashboard_recepcionista_ver_citas.php" class="btn-limpiar">
                                <i class="bi bi-x-circle me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ══ Panel asignar paciente ══ -->
            <?php if ($citaAsignar > 0 && $infoCitaAsignar): ?>
            <div class="panel-asignar" id="panelAsignar">
                <div class="panel-asignar-header">
                    <h3>
                        <i class="bi bi-person-plus-fill me-2" style="color:var(--dorado);"></i>
                        Asignar paciente a la cita
                    </h3>
                    <a href="<?= $urlBase ?>" class="btn-limpiar">
                        <i class="bi bi-x-lg me-1"></i>Cerrar
                    </a>
                </div>

                <div class="panel-asignar-info">
                    <i class="bi bi-calendar2-check me-1" style="color:var(--dorado);"></i>
                    <strong>Cita #<?= $infoCitaAsignar['contCita'] ?></strong> —
                    <?= date('d/m/Y', strtotime($infoCitaAsignar['fechaCita'])) ?>
                    a las <?= substr($infoCitaAsignar['horaInicioCita'], 0, 5) ?> —
                    Dr. <?= htmlspecialchars($infoCitaAsignar['nombreDoctor']) ?>
                </div>

                <!-- Buscador de paciente -->
                <form method="GET" action="dashboard_recepcionista_ver_citas.php">
                    <input type="hidden" name="asignar" value="<?= $citaAsignar ?>">
                    <?php if ($filtroFecha)    echo '<input type="hidden" name="fecha"    value="'.htmlspecialchars($filtroFecha).'">'; ?>
                    <?php if ($filtroEstado)   echo '<input type="hidden" name="estado"   value="'.htmlspecialchars($filtroEstado).'">'; ?>
                    <?php if ($filtroDoctor)   echo '<input type="hidden" name="doctor"   value="'.htmlspecialchars($filtroDoctor).'">'; ?>
                    <?php if ($filtroPaciente) echo '<input type="hidden" name="paciente" value="'.htmlspecialchars($filtroPaciente).'">'; ?>

                    <div class="panel-asignar-busq">
                        <input type="text" name="busq_pac" class="form-control"
                               placeholder="Nombre o número de documento del paciente..."
                               value="<?= htmlspecialchars($busqPac) ?>"
                               autofocus>
                        <button type="submit" class="btn-filtrar">
                            <i class="bi bi-search me-1"></i>Buscar paciente
                        </button>
                    </div>
                </form>

                <!-- Resultados -->
                <?php if ($busqPac !== ''): ?>
                    <?php if (count($resultadosBusq) > 0): ?>
                    <div class="pac-lista">
                        <?php foreach ($resultadosBusq as $pac): ?>
                        <div class="pac-item">
                            <div class="pac-item-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="pac-item-info">
                                <div class="pac-item-nombre">
                                    <?= htmlspecialchars($pac['nameUser'].' '.$pac['secondNameUser']) ?>
                                </div>
                                <div class="pac-item-id">
                                    <?= htmlspecialchars(strtoupper($pac['tipoId']).' '.$pac['idUser']) ?>
                                </div>
                            </div>
                            <form method="POST" action="../Php/asignarPacienteCita.php"
                                  onsubmit="return confirm('¿Asignar a <?= htmlspecialchars(addslashes($pac['nameUser'].' '.$pac['secondNameUser'])) ?> a esta cita?')">
                                <input type="hidden" name="contCita"     value="<?= $citaAsignar ?>">
                                <input type="hidden" name="contPaciente" value="<?= $pac['cont'] ?>">
                                <input type="hidden" name="redirect"
                                       value="dashboard_recepcionista_ver_citas.php<?= $qsFiltros ? '?'.$qsFiltros : '' ?>">
                                <button type="submit" class="btn-asignar-pac">
                                    <i class="bi bi-check-lg me-1"></i>Asignar
                                </button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="pac-vacio">
                        <i class="bi bi-person-x" style="font-size:1.8rem; color:#ddd; display:block; margin-bottom:8px;"></i>
                        No se encontraron pacientes con "<strong><?= htmlspecialchars($busqPac) ?></strong>".
                        Verifica que el usuario tenga rol de paciente.
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                <p style="color:#aaa; font-size:0.88rem; margin-top:4px;">
                    <i class="bi bi-info-circle me-1"></i>
                    Escribe el nombre o número de documento y haz clic en "Buscar paciente".
                </p>
                <?php endif; ?>
            </div>

            <?php elseif ($citaAsignar > 0 && !$infoCitaAsignar): ?>
            <div style="background:#f8d7da; border:1.5px solid #f1aeb5; border-radius:12px; padding:12px 18px; margin-bottom:18px; color:#842029; font-weight:600;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Esa cita no existe o ya no permite modificaciones.
                <a href="<?= $urlBase ?>" style="color:#842029; margin-left:8px; font-weight:700;">Volver</a>
            </div>
            <?php endif; ?>

            <!-- ══ Tabla ══ -->
            <div class="tabla-wrap">
                <div class="tabla-header">
                    <h3><i class="bi bi-table me-2" style="color:var(--dorado);"></i>Listado de citas</h3>
                    <span class="tabla-count"><?= $resCitas->num_rows ?> resultado(s)</span>
                </div>

                <?php if ($resCitas->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paciente</th>
                            <th>Doctor</th>
                            <th>Lugar</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($cita = $resCitas->fetch_assoc()):
                        $esActiva = ($citaAsignar === (int)$cita['contCita']);
                    ?>
                        <tr <?= $esActiva ? 'class="fila-activa"' : '' ?>>
                            <td style="white-space:nowrap; font-weight:600; color:#5a4800;">
                                <?= date('d/m/Y', strtotime($cita['fechaCita'])) ?>
                            </td>
                            <td class="td-hora">
                                <?= substr($cita['horaInicioCita'], 0, 5) ?>
                                <small><?= substr($cita['horaFinalCita'], 0, 5) ?></small>
                            </td>
                            <td>
                                <?php if ($cita['contPaciente']): ?>
                                    <strong><?= htmlspecialchars($cita['nombrePaciente']) ?></strong>
                                    <br><small style="color:#aaa;">
                                        <?= htmlspecialchars(strtoupper($cita['tipoIdPaciente']).' '.$cita['idPaciente']) ?>
                                    </small>
                                <?php else: ?>
                                    <em style="color:#bbb;">Sin asignar</em>
                                <?php endif; ?>
                            </td>
                            <td>Dr. <?= htmlspecialchars($cita['nombreDoctor']) ?></td>
                            <td><?= htmlspecialchars($cita['lugarCita']) ?></td>
                            <td style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                title="<?= htmlspecialchars($cita['motivoCita'] ?? '') ?>">
                                <?= htmlspecialchars($cita['motivoCita'] ?? '—') ?>
                            </td>
                            <td>
                                <span class="badge-estado" style="<?= badgeEstilo($cita['estadoCita']) ?>">
                                    <?= ucfirst($cita['estadoCita']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!in_array($cita['estadoCita'], ['cancelada','completada'])): ?>
                                <div class="acciones">

                                    <!-- Asignar: enlace GET -->
                                    <?php
                                    $urlAsignar = 'dashboard_recepcionista_ver_citas.php?asignar='.$cita['contCita'];
                                    if ($qsFiltros) $urlAsignar .= '&'.$qsFiltros;
                                    // Si ya está abierto para esta cita, el enlace lo cierra
                                    if ($esActiva) $urlAsignar = $urlBase;
                                    ?>
                                    <a href="<?= $urlAsignar ?>#<?= $esActiva ? '' : 'panelAsignar' ?>"
                                       class="btn-accion btn-asignar">
                                        <i class="bi bi-person-plus"></i>
                                        <?= $esActiva ? 'Cerrar' : ($cita['contPaciente'] ? 'Reasignar' : 'Asignar') ?>
                                    </a>

                                    <!-- Cambiar estado: abre modal -->
                                    <button class="btn-accion btn-estado"
                                            onclick="abrirModalEstado(<?= $cita['contCita'] ?>,'<?= $cita['estadoCita'] ?>')">
                                        <i class="bi bi-arrow-repeat"></i> Estado
                                    </button>

                                    <!-- Cancelar: form POST directo -->
                                    <form method="POST" action="../Php/cambiarEstadoCita.php"
                                          style="display:inline;"
                                          onsubmit="return confirm('¿Cancelar esta cita? Esta acción no es fácilmente reversible.')">
                                        <input type="hidden" name="contCita"    value="<?= $cita['contCita'] ?>">
                                        <input type="hidden" name="nuevoEstado" value="cancelada">
                                        <input type="hidden" name="redirect"
                                               value="dashboard_recepcionista_ver_citas.php<?= $qsFiltros ? '?'.$qsFiltros : '' ?>">
                                        <button type="submit" class="btn-accion btn-cancelar">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    </form>

                                </div>
                                <?php else: ?>
                                <span style="color:#bbb; font-size:0.8rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="tabla-vacia">
                    <i class="bi bi-calendar2-x" style="color:#ddd;"></i>
                    <p>No se encontraron citas con los filtros seleccionados.</p>
                    <a href="dashboard_recepcionista_ver_citas.php" class="btn-limpiar" style="margin-top:8px;">
                        Ver todas las citas
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /content-area -->
    </main>

    <!-- ══ Modal cambiar estado (JS mínimo solo para show/hide) ══ -->
    <div class="modal-overlay" id="modalEstado">
        <div class="modal-box">
            <div class="modal-head">
                <h3><i class="bi bi-arrow-repeat me-2" style="color:var(--dorado);"></i>Cambiar estado</h3>
                <button class="modal-close"
                        onclick="document.getElementById('modalEstado').classList.remove('open')">&times;</button>
            </div>
            <form method="POST" action="../Php/cambiarEstadoCita.php">
                <input type="hidden" name="contCita" id="modalContCita">
                <input type="hidden" name="redirect"
                       value="dashboard_recepcionista_ver_citas.php<?= $qsFiltros ? '?'.$qsFiltros : '' ?>">
                <div class="modal-body">
                    <p style="font-size:0.88rem; color:#555; margin-bottom:14px;">
                        Selecciona el nuevo estado para esta cita:
                    </p>
                    <div class="estado-opciones">
                        <div>
                            <input class="estado-radio" type="radio" name="nuevoEstado" id="ep" value="pendiente">
                            <label class="estado-label" for="ep"
                                   style="border-color:#ffd54a; background:#fff3cd; color:#856404;">
                                <i class="bi bi-clock me-1"></i>Pendiente
                            </label>
                        </div>
                        <div>
                            <input class="estado-radio" type="radio" name="nuevoEstado" id="ec" value="confirmada">
                            <label class="estado-label" for="ec"
                                   style="border-color:#a3cfbb; background:#d1e7dd; color:#0f5132;">
                                <i class="bi bi-check-circle me-1"></i>Confirmada
                            </label>
                        </div>
                        <div>
                            <input class="estado-radio" type="radio" name="nuevoEstado" id="ek" value="completada">
                            <label class="estado-label" for="ek"
                                   style="border-color:#9ec5fe; background:#cfe2ff; color:#084298;">
                                <i class="bi bi-check-all me-1"></i>Completada
                            </label>
                        </div>
                        <div>
                            <input class="estado-radio" type="radio" name="nuevoEstado" id="en" value="cancelada">
                            <label class="estado-label" for="en"
                                   style="border-color:#f1aeb5; background:#f8d7da; color:#842029;">
                                <i class="bi bi-x-circle me-1"></i>Cancelada
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-limpiar"
                            onclick="document.getElementById('modalEstado').classList.remove('open')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-filtrar">
                        <i class="bi bi-check-lg me-1"></i>Aplicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Abrir modal de cambiar estado y preseleccionar el estado actual
    function abrirModalEstado(contCita, estadoActual) {
        document.getElementById('modalContCita').value = contCita;
        const radio = document.querySelector('.estado-radio[value="' + estadoActual + '"]');
        if (radio) radio.checked = true;
        document.getElementById('modalEstado').classList.add('open');
    }
    // Cerrar al click en el fondo del overlay
    document.getElementById('modalEstado').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
    </script>
</body>
</html>
