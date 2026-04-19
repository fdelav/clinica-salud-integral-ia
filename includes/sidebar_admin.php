<!-- ══════════════════════════════════════
        SIDEBAR / BARRA LATERAL
        En tu proyecto extrae esto a: includes/sidebar_admin.php
══════════════════════════════════════ -->
<?php
?>
<aside class="sidebar">

    <!-- Marca -->
    <div class="sidebar-brand">
        <img src="../Img/logo clinica salud integral.png" alt="Logo">
        <span class="sidebar-brand-text">Clínica<br>Salud Integral</span>
    </div>

    <!-- Navegación -->
    <nav class="sidebar-nav">

        <!-- Volver a la página principal -->
        <a href="../index.html" class="nav-item back-btn" data-tooltip="Ir al inicio">
            <i class="bi bi-house-fill nav-icon"></i>
            <span class="nav-label">Página principal</span>
        </a>

        <!-- Sección: Panel -->
        <span class="nav-section-label">Panel</span>

        <a href="dashboard_admin.php" class="nav-item <?= $paginaActual === 'inicio' ? 'active' : '' ?>" data-tooltip="Inicio">
            <i class="bi bi-grid-fill nav-icon"></i>
            <span class="nav-label">Inicio</span>
        </a>

        <!-- Sección: Usuarios (se añadirán los CRUD aquí) -->
        <span class="nav-section-label">Usuarios</span>

        <!-- Próximamente: crear, ver, editar, eliminar usuario -->
        <a href="dashboard_admin_crear_usuario.php" class="nav-item <?= $paginaActual === 'crear_usuario' ? 'active' : '' ?>" data-tooltip="Crear usuario"">
            <i class="bi bi-person-plus-fill nav-icon"></i>
            <span class="nav-label">Crear usuario</span>
        </a>

        <a href="dashboard_admin_ver_usuarios.php" class="nav-item <?= $paginaActual === 'ver_usuarios' ? 'active' : '' ?>" data-tooltip="Ver usuarios">
            <i class="bi bi-people-fill nav-icon"></i>
            <span class="nav-label">Ver usuarios</span>
        </a>

        <a href="dashboard_admin_editar_usuario.php" class="nav-item <?= $paginaActual === 'editar_usuario' ? 'active' : '' ?>" data-tooltip="Editar usuario">
            <i class="bi bi-person-gear nav-icon"></i>
            <span class="nav-label">Editar usuario</span>
        </a>

        <a href="dashboard_admin_eliminar_usuario.php" class="nav-item <?= $paginaActual === 'eliminar_usuario' ? 'active' : '' ?>" data-tooltip="Eliminar usuario">
            <i class="bi bi-person-x-fill nav-icon"></i>
            <span class="nav-label">Eliminar usuario</span>
        </a>

    </nav>

    <!-- Footer: info del admin -->
    <div class="sidebar-footer">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="user-info">
            <div class="user-name">Administrador</div>
            <div class="user-role">Admin · Clínica SI</div>
        </div>
    </div>

</aside>