<?php
require_once '../includes/sesiones.php';
verificarRol(['admin']);
 
// ── Búsqueda del usuario ─────────────────────────────────────────────────────
$usuarioEncontrado = false;
$row = [];
 
if (isset($_GET['buscar'])) {
    $idUser = htmlspecialchars($_GET['busq_idUser'] ?? '');
    $tipoId = htmlspecialchars($_GET['busq_tipoId'] ?? '');
 
    require '../Php/coneccion.php';
    $sql    = "SELECT * FROM usuario WHERE tipoId = '$tipoId' AND idUser = '$idUser' LIMIT 1";
    $result = $conn->query($sql);
 
    if ($result && $result->num_rows > 0) {
        $usuarioEncontrado = true;
        $row = $result->fetch_assoc();
    }
}
 
// ── Flash messages ────────────────────────────────────────────────────────────
$flashExito = $_SESSION['editar_exito'] ?? null;
$flashError = $_SESSION['editar_error'] ?? null;
unset($_SESSION['editar_exito'], $_SESSION['editar_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Administrador — Clínica Salud Integral</title>
 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
 
    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>
    <?php $paginaActual = 'editar_usuario'; include '../includes/sidebar_admin.php'; ?>
 
    <main class="main-content">
 
        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-person-gear" style="color:var(--dorado); margin-right:8px;"></i>Editar Usuario</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>
 
        <!-- Área de contenido -->
        <div class="content-area">
            <h1 class="page-title">Editar usuario</h1>
            <p class="page-subtitle">Busca al usuario por su identificación y luego edita sus datos</p>
 
            <!-- Flash messages -->
            <?php if ($flashExito): ?>
            <div class="alert alert-success" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flashExito) ?>
            </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
            <div class="alert alert-danger" role="alert" style="border-radius:12px; margin-bottom:20px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($flashError) ?>
            </div>
            <?php endif; ?>
 
            <!-- PASO 1: Búsqueda -->
            <form class="busqueda-id" method="get" action="dashboard_admin_editar_usuario.php">
 
                <div style="width:100%;">
                    <div class="bi-titulo"><i class="bi bi-search"></i> Paso 1 — Buscar usuario</div>
                </div>
 
                <div class="bi-grupo" style="max-width: 200px;">
                    <label class="form-label" for="busq_tipoId">Tipo de ID</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-card-text"></i>
                        <select class="form-select" id="busq_tipoId" name="busq_tipoId" required>
                            <option value="cc" <?= ($_GET['busq_tipoId'] ?? '') === 'cc' ? 'selected' : '' ?>>Cédula de ciudadanía</option>
                            <option value="ti" <?= ($_GET['busq_tipoId'] ?? '') === 'ti' ? 'selected' : '' ?>>Tarjeta de identidad</option>
                            <option value="ce" <?= ($_GET['busq_tipoId'] ?? '') === 'ce' ? 'selected' : '' ?>>Cédula de extranjería</option>
                        </select>
                    </div>
                </div>
 
                <div class="bi-grupo">
                    <label class="form-label" for="busq_idUser">Número de identificación</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-upc"></i>
                        <input type="text" class="form-control" id="busq_idUser" name="busq_idUser"
                               placeholder="Ej: 1234567890" required
                               value="<?= htmlspecialchars($_GET['busq_idUser'] ?? '') ?>">
                    </div>
                </div>
 
                <input type="hidden" name="buscar" value="1">
 
                <button type="submit" class="btn-primary-clinic" style="padding: 10px 24px;">
                    <i class="bi bi-search"></i> Buscar
                </button>
 
            </form>
 
            <!-- Aviso si se buscó pero no se encontró -->
            <?php if (isset($_GET['buscar']) && !$usuarioEncontrado): ?>
            <div class="aviso-busqueda" style="border-color:#f5a742; background:#fff8ee; color:#7a4500;">
                <i class="bi bi-exclamation-circle-fill"></i>
                No se encontró ningún usuario con ese tipo e número de identificación.
            </div>
            <?php endif; ?>
 
            <!-- PASO 2: Edición -->
            <div class="seccion-edicion">
 
                <div class="seccion-edicion-header">
                    <i class="bi bi-pencil-square" style="font-size:1.3rem; color:var(--dorado);"></i>
                    <h2>Paso 2 — Editar datos</h2>
                </div>
 
                <?php if (!$usuarioEncontrado): ?>
                <div class="aviso-busqueda" id="avisoBusqueda">
                    <i class="bi bi-info-circle-fill"></i>
                    Busca un usuario en el paso 1 para habilitar este formulario y autocompletar sus datos.
                </div>
                <?php else: ?>
                <div class="usuario-encontrado" id="avisoEncontrado">
                    <i class="bi bi-check-circle-fill"></i>
                    Usuario encontrado: <strong><?= htmlspecialchars($row['nameUser'] . ' ' . $row['secondNameUser']) ?></strong> — Edita los campos y guarda los cambios.
                </div>
                <?php endif; ?>
 
                <form action="../Php/editarUserInt.php" method="post">
 
                    <!-- IDs originales para el WHERE del UPDATE -->
                    <input type="hidden" name="idUser_original" value="<?= htmlspecialchars($row['idUser']  ?? '') ?>">
                    <input type="hidden" name="tipoId_original" value="<?= htmlspecialchars($row['tipoID'] ?? '') ?>">
 
                    <!-- Datos personales -->
                    <p class="form-section-label"><i class="bi bi-person"></i> Datos personales</p>
                    <div class="form-grid">
 
                        <div>
                            <label class="form-label" for="nameUser">Nombre</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <input type="text" class="form-control" id="nameUser" name="nameUser"
                                       placeholder="Nombre"
                                       value="<?= htmlspecialchars($row['nameUser'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="secondNameUser">Apellido</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <input type="text" class="form-control" id="secondNameUser" name="secondNameUser"
                                       placeholder="Apellido"
                                       value="<?= htmlspecialchars($row['secondNameUser'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="tipoId">Tipo de ID</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-card-text"></i>
                                <select class="form-select" id="tipoId" name="tipoId"
                                        <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                                    <option value="cc" <?= ($row['tipoId'] ?? '') === 'cc' ? 'selected' : '' ?>>Cédula de ciudadanía</option>
                                    <option value="ti" <?= ($row['tipoId'] ?? '') === 'ti' ? 'selected' : '' ?>>Tarjeta de identidad</option>
                                    <option value="ce" <?= ($row['tipoId'] ?? '') === 'ce' ? 'selected' : '' ?>>Cédula de extranjería</option>
                                </select>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="idUser">Número de identificación</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-upc"></i>
                                <input type="text" class="form-control" id="idUser" name="idUser"
                                       placeholder="Ej: 1234567890"
                                       value="<?= htmlspecialchars($row['idUser'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="fechaNacimientoUsr">Fecha de nacimiento</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-calendar3"></i>
                                <input type="date" class="form-control" id="fechaNacimientoUsr"
                                       name="fechaNacimientoUsr"
                                       value="<?= htmlspecialchars($row['fechaNacimientoUsr'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="generoUser">Género</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-gender-ambiguous"></i>
                                <select class="form-select" id="generoUser" name="generoUser"
                                        <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                                    <option value="m" <?= ($row['generoUser'] ?? '') === 'm' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="f" <?= ($row['generoUser'] ?? '') === 'f' ? 'selected' : '' ?>>Femenino</option>
                                    <option value="o" <?= ($row['generoUser'] ?? '') === 'o' ? 'selected' : '' ?>>Otro</option>
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
                                <input type="email" class="form-control" id="emailUser" name="emailUser"
                                       placeholder="correo@ejemplo.com"
                                       value="<?= htmlspecialchars($row['emailUser'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="telUser">Teléfono</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-telephone-fill"></i>
                                <input type="text" class="form-control" id="telUser" name="telUser"
                                       placeholder="Ej: 3001234567"
                                       value="<?= htmlspecialchars($row['telUser'] ?? '') ?>"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                            </div>
                        </div>
 
                    </div>
 
                    <!-- Seguridad -->
                    <p class="form-section-label"><i class="bi bi-lock"></i> Seguridad</p>
                    <div class="form-grid">
 
                        <div>
                            <label class="form-label" for="passwordUser">Nueva contraseña</label>
                            <div class="password-wrap input-icon-wrap">
                                <i class="bi bi-lock-fill"></i>
                                <input type="password" class="form-control" id="passwordUser"
                                       name="passwordUser" placeholder="Dejar vacío para no cambiar"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                                <button type="button" class="btn-toggle-pass"
                                        onclick="togglePass('passwordUser', this)" tabindex="-1">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
 
                        <div>
                            <label class="form-label" for="repeatPasswordUser">Repetir nueva contraseña</label>
                            <div class="password-wrap input-icon-wrap">
                                <i class="bi bi-lock-fill"></i>
                                <input type="password" class="form-control" id="repeatPasswordUser"
                                       name="repeatPasswordUser" placeholder="Repite la contraseña"
                                       <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                                <button type="button" class="btn-toggle-pass"
                                        onclick="togglePass('repeatPasswordUser', this)" tabindex="-1">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
 
                    </div>
 
                    <!-- Rol -->
                    <p class="form-section-label"><i class="bi bi-shield"></i> Rol del usuario</p>
                    <div class="form-grid">
 
                        <div>
                            <label class="form-label" for="rolUser">Rol</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-briefcase-fill"></i>
                                <select class="form-select" id="rolUser" name="rolUser"
                                        <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                                    <option value="na"        <?= ($row['rolUser'] ?? '') === 'na'        ? 'selected' : '' ?>>Sin rol</option>
                                    <option value="doctor"    <?= ($row['rolUser'] ?? '') === 'doctor'    ? 'selected' : '' ?>>Doctor</option>
                                    <option value="enfermero" <?= ($row['rolUser'] ?? '') === 'enfermero' ? 'selected' : '' ?>>Enfermero</option>
                                    <option value="secretario"<?= ($row['rolUser'] ?? '') === 'secretario'? 'selected' : '' ?>>Secretario</option>
                                    <option value="diseñador" <?= ($row['rolUser'] ?? '') === 'diseñador' ? 'selected' : '' ?>>Diseñador</option>
                                    <option value="admin"     <?= ($row['rolUser'] ?? '') === 'admin'     ? 'selected' : '' ?>>Administrador</option>
                                </select>
                            </div>
                        </div>
 
                    </div>
 
                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="submit" class="btn-primary-clinic"
                                <?= !$usuarioEncontrado ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '' ?>>
                            <i class="bi bi-floppy-fill"></i> Guardar cambios
                        </button>
                        <a href="dashboard_admin_ver_usuarios.php" class="btn-secondary-clinic">Cancelar</a>
                    </div>
 
                </form>
            </div>
        </div>
    </main>
 
    <script>
        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type      = 'text';
                icon.className  = 'bi bi-eye';
            } else {
                input.type      = 'password';
                icon.className  = 'bi bi-eye-slash';
            }
        }
    </script>
 
</body>
</html>