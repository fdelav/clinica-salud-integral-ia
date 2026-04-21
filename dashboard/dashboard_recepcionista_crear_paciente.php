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

</head>
<body>
    <?php $paginaActual = 'ver_citas'; include '../includes/sidebar_recepcionista.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-calendar2-week-fill" style="color:var(--dorado); margin-right:8px;"></i>Citas</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-headset"></i> Recepcionista</span>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">Nuevo usuario</h1>
            <p class="page-subtitle">Completa los campos para registrar un usuario en el sistema</p>

            <form action="../php/registroUserInt.php" method="post">

                <!-- Datos personales -->
                <p class="form-section-label"><i class="bi bi-person"></i> Datos personales</p>
                <div class="form-grid">

                    <div>
                        <label class="form-label" for="nameUser">Nombre</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" class="form-control" id="nameUser"
                                   name="nameUser" placeholder="Nombre" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="secondNameUser">Apellido</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" class="form-control" id="secondNameUser"
                                   name="secondNameUser" placeholder="Apellido" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="tipoId">Tipo de ID</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-card-text"></i>
                            <select class="form-select" id="tipoId" name="tipoId" required>
                                <option value="cc">Cédula de ciudadanía</option>
                                <option value="ti">Tarjeta de identidad</option>
                                <option value="ce">Cédula de extranjería</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="idUser">Número de identificación</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-upc"></i>
                            <input type="text" class="form-control" id="idUser"
                                   name="idUser" placeholder="Ej: 1234567890" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="fechaNacimientoUsr">Fecha de nacimiento</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3"></i>
                            <input type="date" class="form-control" id="fechaNacimientoUsr"
                                   name="fechaNacimientoUsr" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="generoUser">Género</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-gender-ambiguous"></i>
                            <select class="form-select" id="generoUser" name="generoUser" required>
                                <option value="m">Masculino</option>
                                <option value="f">Femenino</option>
                                <option value="o">Otro</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Contacto -->
                <p class="form-section-label"><i class="bi bi-envelope"></i> Contacto</p>
                <div class="form-grid">

                    <div>
                        <label class="form-label" for="emailUser">Correo electrónico</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope-fill"></i>
                            <input type="email" class="form-control" id="emailUser"
                                   name="emailUser" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="telUser">Teléfono</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone-fill"></i>
                            <input type="text" class="form-control" id="telUser"
                                   name="telUser" placeholder="Ej: 3001234567" required>
                        </div>
                    </div>

                </div>

                <!-- Seguridad -->
                <p class="form-section-label"><i class="bi bi-lock"></i> Seguridad</p>
                <div class="form-grid">

                    <div>
                        <label class="form-label" for="passwordUser">Contraseña</label>
                        <div class="password-wrap input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" class="form-control" id="passwordUser"
                                   name="passwordUser" placeholder="Mínimo 8 caracteres" required>
                            <button type="button" class="btn-toggle-pass" onclick="togglePass('passwordUser', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="repeatPasswordUser">Repetir contraseña</label>
                        <div class="password-wrap input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" class="form-control" id="repeatPasswordUser"
                                   name="repeatPasswordUser" placeholder="Repite la contraseña" required>
                            <button type="button" class="btn-toggle-pass" onclick="togglePass('repeatPasswordUser', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Rol -->
                <input type="hidden" name="rolUser" value="paciente">

                <!-- Acciones -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary-clinic">
                        <i class="bi bi-check-circle me-2"></i>Crear usuario
                    </button>
                    <a href="dashboard_admin.php" class="btn-secondary-clinic">Cancelar</a>
                </div>

            </form>
        </div><!-- /content-area -->
    </main>
</body>
</html>
