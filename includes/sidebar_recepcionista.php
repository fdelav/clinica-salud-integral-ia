<?php
// includes/sidebar_recepcionista.php
// Uso: <?php $paginaActual = 'inicio'; include '../includes/sidebar_recepcionista.php'; ?>
<aside class="sidebar">

    <!-- Marca -->
    <div class="sidebar-brand">
        <img src="../Img/logo clinica salud integral.png" alt="Logo">
        <span class="sidebar-brand-text">Clínica<br>Salud Integral</span>
    </div>

    <!-- Navegación -->
    <nav class="sidebar-nav">

        <!-- Volver a la página principal -->
        <a href="../index.php" class="nav-item back-btn" data-tooltip="Ir al inicio">
            <i class="bi bi-house-fill nav-icon"></i>
            <span class="nav-label">Página principal</span>
        </a>

        <!-- Sección: Panel -->
        <span class="nav-section-label">Panel</span>

        <a href="dashboard_recepcionista.php"
           class="nav-item <?= $paginaActual === 'inicio' ? 'active' : '' ?>"
           data-tooltip="Inicio">
            <i class="bi bi-grid-fill nav-icon"></i>
            <span class="nav-label">Inicio</span>
        </a>

        <!-- Sección: Usuarios -->
        <span class="nav-section-label">Usuarios</span>

        <a href="dashboard_recepcionista_ver_usuarios.php"
           class="nav-item <?= $paginaActual === 'ver_usuarios' ? 'active' : '' ?>"
           data-tooltip="Ver usuarios">
            <i class="bi bi-people-fill nav-icon"></i>
            <span class="nav-label">Ver usuarios</span>
        </a>

        <a href="dashboard_recepcionista_crear_paciente.php"
           class="nav-item <?= $paginaActual === 'crear_paciente' ? 'active' : '' ?>"
           data-tooltip="Crear paciente">
            <i class="bi bi-person-plus-fill nav-icon"></i>
            <span class="nav-label">Crear paciente</span>
        </a>

        <a href="dashboard_recepcionista_promover_paciente.php"
           class="nav-item <?= $paginaActual === 'promover_paciente' ? 'active' : '' ?>"
           data-tooltip="Promover a paciente">
            <i class="bi bi-person-check-fill nav-icon"></i>
            <span class="nav-label">Promover a paciente</span>
        </a>

        <!-- Sección: Citas -->
        <span class="nav-section-label">Citas</span>

        <a href="dashboard_recepcionista_crear_cita.php"
           class="nav-item <?= $paginaActual === 'crear_cita' ? 'active' : '' ?>"
           data-tooltip="Crear cita">
            <i class="bi bi-calendar-plus-fill nav-icon"></i>
            <span class="nav-label">Crear cita</span>
        </a>

        <a href="dashboard_recepcionista_ver_citas.php"
           class="nav-item <?= $paginaActual === 'ver_citas' ? 'active' : '' ?>"
           data-tooltip="Ver citas">
            <i class="bi bi-calendar2-week-fill nav-icon"></i>
            <span class="nav-label">Ver citas</span>
        </a>

    </nav>

    <!-- Footer: info de la recepcionista -->
    <div class="sidebar-footer">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Recepcionista') ?></div>
            <div class="user-role">Recepcionista · Clínica SI</div>
        </div>
    </div>

</aside>
