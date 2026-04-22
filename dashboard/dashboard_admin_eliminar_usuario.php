<?php
require_once '../includes/sesiones.php';
verificarRol(['admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar Usuario — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../Css/dashboard.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
    <link href="../Css/ver_usuario.css" rel="stylesheet">
</head>
<body>
    <?php $paginaActual = 'eliminar_usuario'; include '../includes/sidebar_admin.php'; ?>

    <main class="main-content">

        <div class="topbar">
            <h2><i class="bi bi-person-x-fill" style="color:var(--dorado); margin-right:8px;"></i>Eliminar Usuario</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">Eliminar usuario del sistema</h1>
            <p class="page-subtitle">Busca al usuario por tipo e identificación para confirmar su eliminación.</p>

            <!-- Formulario de búsqueda -->
            <form class="busqueda-directa" method="GET" action="dashboard_admin_eliminar_usuario.php">
                <div style="width:100%;">
                    <div class="bd-titulo"><i class="bi bi-search"></i> Buscar usuario por identificación</div>
                </div>
                <div class="bd-grupo" style="max-width:200px;">
                    <label for="busq_tipo">Tipo de ID</label>
                    <select name="tipoId" id="busq_tipo" required>
                        <option value="">Seleccione...</option>
                        <option value="cc">Cédula de ciudadanía</option>
                        <option value="ti">Tarjeta de identidad</option>
                        <option value="ce">Cédula de extranjería</option>
                    </select>
                </div>
                <div class="bd-grupo bd-grupo-id">
                    <label for="busq_id">Número de identificación</label>
                    <input type="text" name="idUser" id="busq_id"
                           placeholder="Ej: 1234567890" required>
                </div>
                <button type="submit" class="bd-btn">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="dashboard_admin_eliminar_usuario.php" class="bd-btn-limpiar"
                   style="display:inline-flex; align-items:center; gap:4px; text-decoration:none;">
                    <i class="bi bi-x"></i> Limpiar
                </a>
            </form>

        </div>
    </main>

    <?php include 'modal_confirmar_eliminacion.php'; ?>

</body>
</html>