<?php
require_once '../includes/sesiones.php';
verificarRol(['recepcionista']);

require '../Php/coneccion.php';

// ── Flash messages ───────────────────────────────────────────────────────────
$flashExito = $_SESSION['recep_exito'] ?? null;
$flashError = $_SESSION['recep_error'] ?? null;
unset($_SESSION['recep_exito'], $_SESSION['recep_error']);

// ── Búsqueda por tipoId + idUser ─────────────────────────────────────────────
$busqTipo = $_GET['busq_tipoId'] ?? '';
$busqId   = trim($_GET['busq_idUser'] ?? '');
$usuario  = null;
$buscado  = isset($_GET['buscar']);

if ($buscado && $busqTipo !== '' && $busqId !== '') {
    $stmt = $conn->prepare(
        "SELECT cont, nameUser, secondNameUser, tipoId, idUser,
                emailUser, telUser, generoUser, fechaNacimientoUsr, rolUser
         FROM usuario
         WHERE tipoId = ? AND idUser = ?
         LIMIT 1"
    );
    $stmt->bind_param('ss', $busqTipo, $busqId);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->num_rows ? $res->fetch_assoc() : null;
}

$conn->close();

// Etiquetas legibles
$tiposId = ['cc' => 'Cédula de Ciudadanía', 'ti' => 'Tarjeta de Identidad', 'ce' => 'Cédula de Extranjería'];
$generos  = ['m' => 'Masculino', 'f' => 'Femenino', 'o' => 'Otro'];
$roles    = [
    'na'          => 'Visitante',
    'paciente'    => 'Paciente',
    'doctor'      => 'Doctor',
    'enfermero'   => 'Enfermero',
    'secretario'  => 'Recepcionista',
    'diseñador'   => 'Diseñador',
    'admin'       => 'Administrador',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promover Paciente — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css"  rel="stylesheet">

    <style>
        /* Ficha de datos del usuario encontrado */
        .ficha-usuario {
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 18px;
            overflow: hidden;
            max-width: 680px;
            margin-top: 24px;
        }
        .ficha-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 24px;
            background: #fffdf0;
            border-bottom: 1px solid var(--amarillo-borde);
        }
        .ficha-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--amarillo-claro);
            border: 2px solid var(--amarillo-borde);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--dorado);
            flex-shrink: 0;
        }
        .ficha-nombre {
            font-family: "Montserrat", sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            color: #3a2f00;
        }
        .ficha-id {
            font-size: 0.82rem;
            color: #888;
            margin-top: 2px;
        }
        .ficha-body {
            padding: 22px 24px;
        }
        .ficha-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 24px;
            margin-bottom: 22px;
        }
        .ficha-campo label {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--dorado);
            display: block;
            margin-bottom: 3px;
        }
        .ficha-campo span {
            font-size: 0.9rem;
            color: #333;
        }

        /* Badge de rol actual */
        .badge-rol {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
        }
        .badge-na       { background: #e2e3e5; color: #41464b; }
        .badge-paciente { background: #d1e7dd; color: #0f5132; }
        .badge-otro     { background: #cfe2ff; color: #084298; }

        /* Zona de acción / aviso */
        .ficha-accion {
            border-top: 1.5px dashed var(--amarillo-borde);
            padding-top: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .aviso-rol {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fffdf0;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 0.88rem;
            color: #7a6000;
        }
        .aviso-rol.ya-paciente {
            background: #d1e7dd;
            border-color: #a3cfbb;
            color: #0f5132;
        }
        .aviso-rol.otro-rol {
            background: #cfe2ff;
            border-color: #9ec5fe;
            color: #084298;
        }

        .btn-promover {
            background: var(--amarillo);
            border: none;
            border-radius: 25px;
            padding: 11px 30px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.18s, transform 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-promover:hover {
            background: var(--amarillo-hover);
            transform: scale(1.02);
        }

        /* Resultado vacío */
        .no-encontrado {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 40px 20px;
            color: #bbb;
            text-align: center;
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 18px;
            max-width: 680px;
            margin-top: 24px;
        }
        .no-encontrado i { font-size: 2.2rem; }
        .no-encontrado p { margin: 0; font-size: 0.92rem; }

        @media (max-width: 576px) {
            .ficha-grid { grid-template-columns: 1fr; }
            .busqueda-id { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    <?php $paginaActual = 'promover_paciente'; include '../includes/sidebar_recepcionista.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-person-check-fill" style="color:var(--dorado); margin-right:8px;"></i>Promover paciente</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-headset"></i> Recepcionista</span>
            </div>
        </div>

        <div class="content-area">

            <h1 class="page-title">Promover visitante a paciente</h1>
            <p class="page-subtitle">Busca al visitante por su tipo y número de documento para promoverlo.</p>

            <!-- Flash messages -->
            <?php if ($flashExito): ?>
            <div style="background:#d1e7dd; border:1.5px solid #a3cfbb; border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#0f5132; font-weight:600; max-width:680px;">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($flashExito) ?>
            </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
            <div style="background:#f8d7da; border:1.5px solid #f1aeb5; border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#842029; font-weight:600; max-width:680px;">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($flashError) ?>
            </div>
            <?php endif; ?>

            <!-- ── Formulario de búsqueda ── -->
            <form method="GET" action="dashboard_recepcionista_promover_paciente.php">
                <input type="hidden" name="buscar" value="1">
                <div class="busqueda-id" style="max-width:680px;">
                    <div class="bi-grupo" style="max-width:200px;">
                        <span class="bi-titulo">Tipo de documento</span>
                        <select name="busq_tipoId" class="form-select" required>
                            <option value="">— Tipo —</option>
                            <option value="cc" <?= $busqTipo==='cc' ? 'selected':'' ?>>CC — Cédula de Ciudadanía</option>
                            <option value="ti" <?= $busqTipo==='ti' ? 'selected':'' ?>>TI — Tarjeta de Identidad</option>
                            <option value="ce" <?= $busqTipo==='ce' ? 'selected':'' ?>>CE — Cédula de Extranjería</option>
                        </select>
                    </div>
                    <div class="bi-grupo">
                        <span class="bi-titulo">Número de documento</span>
                        <input type="text" name="busq_idUser" class="form-control"
                               placeholder="Ej: 1234567890"
                               value="<?= htmlspecialchars($busqId) ?>"
                               required>
                    </div>
                    <button type="submit" class="btn-primary-clinic"
                            style="width:auto; padding:10px 26px; margin-top:0; align-self:flex-end;">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    <?php if ($buscado): ?>
                    <a href="dashboard_recepcionista_promover_paciente.php"
                       class="btn-secondary-clinic" style="align-self:flex-end;">
                        <i class="bi bi-x-circle me-1"></i>Limpiar
                    </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- ── Resultado de búsqueda ── -->
            <?php if ($buscado): ?>

                <?php if ($usuario): ?>
                <!-- Usuario encontrado -->
                <div class="ficha-usuario">
                    <div class="ficha-header">
                        <div class="ficha-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="ficha-nombre">
                                <?= htmlspecialchars($usuario['nameUser'].' '.$usuario['secondNameUser']) ?>
                            </div>
                            <div class="ficha-id">
                                <?= htmlspecialchars($tiposId[$usuario['tipoId']] ?? strtoupper($usuario['tipoId'])) ?>
                                · <?= htmlspecialchars($usuario['idUser']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="ficha-body">
                        <div class="ficha-grid">
                            <div class="ficha-campo">
                                <label>Correo electrónico</label>
                                <span><?= htmlspecialchars($usuario['emailUser']) ?></span>
                            </div>
                            <div class="ficha-campo">
                                <label>Teléfono</label>
                                <span><?= htmlspecialchars($usuario['telUser'] ?: '—') ?></span>
                            </div>
                            <div class="ficha-campo">
                                <label>Género</label>
                                <span><?= htmlspecialchars($generos[$usuario['generoUser']] ?? '—') ?></span>
                            </div>
                            <div class="ficha-campo">
                                <label>Fecha de nacimiento</label>
                                <span>
                                    <?= $usuario['fechaNacimientoUsr']
                                        ? date('d/m/Y', strtotime($usuario['fechaNacimientoUsr']))
                                        : '—' ?>
                                </span>
                            </div>
                            <div class="ficha-campo">
                                <label>Rol actual</label>
                                <?php
                                $rolActual = $usuario['rolUser'];
                                $badgeClass = match($rolActual) {
                                    'na'       => 'badge-na',
                                    'paciente' => 'badge-paciente',
                                    default    => 'badge-otro',
                                };
                                ?>
                                <span>
                                    <span class="badge-rol <?= $badgeClass ?>">
                                        <?= htmlspecialchars($roles[$rolActual] ?? $rolActual) ?>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- Zona de acción según rol actual -->
                        <div class="ficha-accion">
                            <?php if ($rolActual === 'na'): ?>
                                <!-- Visitante: puede promoverse -->
                                <form method="POST" action="../Php/promoverPaciente.php"
                                      onsubmit="return confirm('¿Promover a <?= htmlspecialchars(addslashes($usuario['nameUser'].' '.$usuario['secondNameUser'])) ?> como paciente?')">
                                    <input type="hidden" name="cont" value="<?= $usuario['cont'] ?>">
                                    <input type="hidden" name="redirect"
                                           value="dashboard_recepcionista_promover_paciente.php?buscar=1&busq_tipoId=<?= urlencode($busqTipo) ?>&busq_idUser=<?= urlencode($busqId) ?>">
                                    <button type="submit" class="btn-promover">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                        Promover a paciente
                                    </button>
                                </form>
                                <span style="font-size:0.82rem; color:#aaa;">
                                    El rol cambiará de <strong>Visitante</strong> a <strong>Paciente</strong>.
                                </span>

                            <?php elseif ($rolActual === 'paciente'): ?>
                                <div class="aviso-rol ya-paciente">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Este usuario ya tiene el rol de <strong>Paciente</strong>. No es necesario promoverlo.
                                </div>

                            <?php else: ?>
                                <div class="aviso-rol otro-rol">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Este usuario tiene el rol de <strong><?= htmlspecialchars($roles[$rolActual] ?? $rolActual) ?></strong>.
                                    Solo los visitantes (rol "na") pueden ser promovidos a paciente desde aquí.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php else: ?>
                <!-- No encontrado -->
                <div class="no-encontrado">
                    <i class="bi bi-person-x-fill" style="color:#ddd;"></i>
                    <p>No se encontró ningún usuario con
                        <strong><?= htmlspecialchars(strtoupper($busqTipo)) ?> <?= htmlspecialchars($busqId) ?></strong>.
                    </p>
                    <p style="font-size:0.82rem;">Verifica el tipo y número de documento e intenta de nuevo.</p>
                </div>
                <?php endif; ?>

            <?php endif; ?>

        </div><!-- /content-area -->
    </main>
</body>
</html>
