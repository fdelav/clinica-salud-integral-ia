<?php
require_once '../includes/sesiones.php';
verificarRol(['admin']);

require '../Php/coneccion.php';

// Flash messages
$flashExito = $_SESSION['admin_exito'] ?? null;
$flashError = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_exito'], $_SESSION['admin_error']);

// ── Búsqueda directa por tipoId + idUser ────────────────────────────────────
$busqTipo = $_GET['busq_tipo'] ?? '';
$busqId   = trim($_GET['busq_id'] ?? '');

$where  = ['1=1'];
$params = [];
$types  = '';

if ($busqTipo !== '') {
    $where[]  = 'tipoId = ?';
    $params[] = $busqTipo;
    $types   .= 's';
}
if ($busqId !== '') {
    $where[]  = 'idUser LIKE ?';
    $params[] = '%' . $busqId . '%';
    $types   .= 's';
}

$whereSQL = implode(' AND ', $where);

$sql = "SELECT cont, nameUser, secondNameUser, tipoId, idUser,
               generoUser, fechaNacimientoUsr, emailUser, telUser, rolUser
        FROM usuario
        WHERE $whereSQL
        ORDER BY nameUser, secondNameUser";

$stmt = $conn->prepare($sql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$resUsuarios = $stmt->get_result();

$conn->close();

// ── Helpers ──────────────────────────────────────────────────────────────────
function calcularEdad(?string $fecha): string {
    if (!$fecha) return '—';
    return (string)(new DateTime())->diff(new DateTime($fecha))->y;
}

$rolesLabel = [
    'na'         => 'Visitante',
    'paciente'   => 'Paciente',
    'doctor'     => 'Doctor',
    'enfermero'  => 'Enfermero',
    'secretario' => 'Secretario',
    'diseñador'  => 'Diseñador',
    'admin'      => 'Administrador',
];
$generosLabel = ['m' => 'Masculino', 'f' => 'Femenino', 'o' => 'Otro'];

function rolClass(string $rol): string {
    return match($rol) {
        'diseñador' => 'rol-disenador',
        default     => 'rol-' . $rol,
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Usuarios — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css"     rel="stylesheet">
    <link href="../Css/my_style.css"      rel="stylesheet">
    <link href="../Css/ver_usuario.css"   rel="stylesheet">
</head>
<body>
    <?php $paginaActual = 'ver_usuarios'; include '../includes/sidebar_admin.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-people-fill" style="color:var(--dorado); margin-right:8px;"></i>Ver Usuarios</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">Usuarios del sistema</h1>
            <p class="page-subtitle">Consulta, filtra y gestiona todos los usuarios registrados</p>

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

            <!-- ── Búsqueda directa ── -->
            <form class="busqueda-directa" method="GET" action="dashboard_admin_ver_usuarios.php">
                <div style="width:100%;">
                    <div class="bd-titulo"><i class="bi bi-search"></i> Búsqueda directa por identificación</div>
                </div>
                <div class="bd-grupo" style="max-width:200px;">
                    <label for="busq_tipo">Tipo de ID</label>
                    <select name="busq_tipo" id="busq_tipo">
                        <option value="">Todos</option>
                        <option value="cc" <?= $busqTipo==='cc' ? 'selected':'' ?>>Cédula de ciudadanía</option>
                        <option value="ti" <?= $busqTipo==='ti' ? 'selected':'' ?>>Tarjeta de identidad</option>
                        <option value="ce" <?= $busqTipo==='ce' ? 'selected':'' ?>>Cédula de extranjería</option>
                    </select>
                </div>
                <div class="bd-grupo bd-grupo-id">
                    <label for="busq_id">Número de identificación</label>
                    <input type="text" name="busq_id" id="busq_id"
                           placeholder="Ej: 1234567890"
                           value="<?= htmlspecialchars($busqId) ?>">
                </div>
                <button type="submit" class="bd-btn">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="dashboard_admin_ver_usuarios.php" class="bd-btn-limpiar"
                   style="display:inline-flex; align-items:center; gap:4px; text-decoration:none;">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            </form>

            <!-- ── Toolbar ── -->
            <div class="tabla-toolbar">
                <div class="tabla-info">
                    Mostrando <strong id="infoDesde">1</strong>–<strong id="infoHasta">10</strong>
                    de <strong id="infoTotal">0</strong> usuarios
                </div>
                <div class="entradas-selector">
                    Mostrar
                    <select id="selectEntradas" onchange="cambiarEntradas(this.value)">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    por página
                </div>
            </div>

            <!-- Chips de filtros activos -->
            <div class="filtros-activos" id="filtrosActivos"></div>

            <!-- ── Tabla ── -->
            <div class="tabla-wrapper">
                <table id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th style="position:relative;">
                                <div class="th-inner">Nombre
                                    <button class="btn-filtro" data-col="nombre" onclick="toggleFiltro('filtro-nombre')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-nombre">
                                    <label>Condición</label>
                                    <select id="cond-nombre"><option value="contiene">Contiene</option><option value="igual">Igual a</option><option value="empieza">Empieza por</option></select>
                                    <label>Valor</label>
                                    <input type="text" id="val-nombre" placeholder="Ej: Carlos">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('nombre')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('nombre')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Apellido
                                    <button class="btn-filtro" data-col="apellido" onclick="toggleFiltro('filtro-apellido')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-apellido">
                                    <label>Condición</label>
                                    <select id="cond-apellido"><option value="contiene">Contiene</option><option value="igual">Igual a</option><option value="empieza">Empieza por</option></select>
                                    <label>Valor</label>
                                    <input type="text" id="val-apellido" placeholder="Ej: García">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('apellido')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('apellido')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Identificación
                                    <button class="btn-filtro" data-col="identificacion" onclick="toggleFiltro('filtro-identificacion')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-identificacion">
                                    <label>Tipo de ID</label>
                                    <select id="val-tipo-id"><option value="">Todos</option><option value="cc">Cédula de ciudadanía</option><option value="ti">Tarjeta de identidad</option><option value="ce">Cédula de extranjería</option></select>
                                    <label>Número</label>
                                    <input type="text" id="val-identificacion" placeholder="Ej: 1234567890">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('identificacion')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('identificacion')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Género
                                    <button class="btn-filtro" data-col="genero" onclick="toggleFiltro('filtro-genero')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-genero">
                                    <label>Género</label>
                                    <select id="val-genero"><option value="">Todos</option><option value="m">Masculino</option><option value="f">Femenino</option><option value="o">Otro</option></select>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('genero')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('genero')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Edad
                                    <button class="btn-filtro" data-col="edad" onclick="toggleFiltro('filtro-edad')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-edad">
                                    <label>Condición</label>
                                    <select id="cond-edad"><option value="igual">Igual a</option><option value="mayor">Mayor que</option><option value="menor">Menor que</option><option value="entre">Entre</option></select>
                                    <label>Valor</label>
                                    <input type="number" id="val-edad" placeholder="Ej: 30" min="0" max="120">
                                    <span id="edad-hasta-wrap" style="display:none;">
                                        <label>Hasta</label>
                                        <input type="number" id="val-edad-hasta" placeholder="Ej: 50" min="0" max="120">
                                    </span>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('edad')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('edad')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Correo
                                    <button class="btn-filtro" data-col="correo" onclick="toggleFiltro('filtro-correo')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-correo">
                                    <label>Condición</label>
                                    <select id="cond-correo"><option value="contiene">Contiene</option><option value="igual">Igual a</option><option value="empieza">Empieza por</option></select>
                                    <label>Valor</label>
                                    <input type="text" id="val-correo" placeholder="Ej: usuario@correo.com">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('correo')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('correo')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Teléfono
                                    <button class="btn-filtro" data-col="telefono" onclick="toggleFiltro('filtro-telefono')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-telefono">
                                    <label>Condición</label>
                                    <select id="cond-telefono"><option value="contiene">Contiene</option><option value="empieza">Empieza por</option></select>
                                    <label>Valor</label>
                                    <input type="text" id="val-telefono" placeholder="Ej: 310">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('telefono')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('telefono')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">Rol
                                    <button class="btn-filtro" data-col="rol" onclick="toggleFiltro('filtro-rol')" title="Filtrar"><i class="bi bi-funnel"></i></button>
                                </div>
                                <div class="filtro-panel" id="filtro-rol">
                                    <label>Rol</label>
                                    <select id="val-rol">
                                        <option value="">Todos</option>
                                        <option value="admin">Administrador</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="enfermero">Enfermero</option>
                                        <option value="secretario">Secretario</option>
                                        <option value="diseñador">Diseñador</option>
                                        <option value="paciente">Paciente</option>
                                        <option value="na">Visitante</option>
                                    </select>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('rol')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('rol')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th><div class="th-inner">Acciones</div></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                    <?php if ($resUsuarios->num_rows > 0): ?>
                        <?php while ($u = $resUsuarios->fetch_assoc()):
                            $edad   = calcularEdad($u['fechaNacimientoUsr']);
                            $rolKey = $u['rolUser'];
                            $rolLbl = $rolesLabel[$rolKey] ?? $rolKey;
                            $genLbl = $generosLabel[$u['generoUser']] ?? '—';
                        ?>
                        <tr data-nombre="<?= htmlspecialchars($u['nameUser']) ?>"
                            data-apellido="<?= htmlspecialchars($u['secondNameUser']) ?>"
                            data-tipo-id="<?= htmlspecialchars($u['tipoId']) ?>"
                            data-id="<?= htmlspecialchars($u['idUser']) ?>"
                            data-genero="<?= htmlspecialchars($u['generoUser']) ?>"
                            data-edad="<?= $edad !== '—' ? $edad : '' ?>"
                            data-correo="<?= htmlspecialchars($u['emailUser']) ?>"
                            data-telefono="<?= htmlspecialchars($u['telUser'] ?? '') ?>"
                            data-rol="<?= htmlspecialchars($rolKey) ?>">
                            <td><?= htmlspecialchars($u['nameUser']) ?></td>
                            <td><?= htmlspecialchars($u['secondNameUser']) ?></td>
                            <td>
                                <span class="badge-id"><?= strtoupper($u['tipoId']) ?></span>
                                <?= htmlspecialchars($u['idUser']) ?>
                            </td>
                            <td>
                                <span class="badge-genero badge-<?= htmlspecialchars($u['generoUser']) ?>">
                                    <?= $genLbl ?>
                                </span>
                            </td>
                            <td><?= $edad ?></td>
                            <td><?= htmlspecialchars($u['emailUser']) ?></td>
                            <td><?= htmlspecialchars($u['telUser'] ?? '—') ?></td>
                            <td>
                                <span class="badge-rol <?= rolClass($rolKey) ?>">
                                    <?= htmlspecialchars($rolLbl) ?>
                                </span>
                            </td>
                            <td>
                                <div class="td-acciones">
                                    <a href="dashboard_admin_editar_usuario.php?tipoId=<?= urlencode($u['tipoId']) ?>&idUser=<?= urlencode($u['idUser']) ?>"
                                       class="btn-accion btn-editar">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <a href="dashboard_admin_eliminar_usuario.php?tipoId=<?= urlencode($u['tipoId']) ?>&idUser=<?= urlencode($u['idUser']) ?>"
                                       class="btn-accion btn-eliminar">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:48px 20px; color:#bbb;">
                                <i class="bi bi-people" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                                No se encontraron usuarios<?= ($busqTipo || $busqId) ? ' con esos criterios' : '' ?>.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- ── Paginación ── -->
            <div class="paginacion-wrapper">
                <div class="paginacion-info" id="paginacionInfo">Página 1 de 1</div>
                <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                    <div class="paginacion" id="paginacion"></div>
                    <div class="pag-goto">
                        Ir a página
                        <input type="number" id="inputPagina" min="1" value="1">
                        <button onclick="irAPagina()">Ir</button>
                    </div>
                </div>
            </div>

        </div><!-- /content-area -->
    </main>

    <script src="../Js/ver_usuario.js"></script>
</body>
</html>
