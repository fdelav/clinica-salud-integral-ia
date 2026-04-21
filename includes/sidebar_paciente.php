<!-- ══════════════════════════════════════
        SIDEBAR / BARRA LATERAL — PACIENTE
        Incluir desde: includes/sidebar_paciente.php
══════════════════════════════════════ -->
<?php ?>
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

        <a href="dashboard_paciente.php" class="nav-item <?= $paginaActual === 'inicio' ? 'active' : '' ?>" data-tooltip="Inicio">
            <i class="bi bi-grid-fill nav-icon"></i>
            <span class="nav-label">Inicio</span>
        </a>

        <!-- Sección: Citas -->
        <span class="nav-section-label">Mis citas</span>

        <a href="dashboard_paciente_agendar_cita.php" class="nav-item <?= $paginaActual === 'agendar_cita' ? 'active' : '' ?>" data-tooltip="Agendar cita">
            <i class="bi bi-calendar-plus-fill nav-icon"></i>
            <span class="nav-label">Agendar cita</span>
        </a>

        <a href="dashboard_paciente_gestionar_citas.php" class="nav-item <?= $paginaActual === 'gestionar_citas' ? 'active' : '' ?>" data-tooltip="Gestionar citas">
            <i class="bi bi-calendar2-week-fill nav-icon"></i>
            <span class="nav-label">Mis citas</span>
        </a>

        <!-- Sección: Perfil -->
        <span class="nav-section-label">Cuenta</span>

        <a href="../includes/cerrar_sesion.php" class="nav-item" data-tooltip="Cerrar sesión" style="color:#c0392b;">
            <i class="bi bi-box-arrow-left nav-icon"></i>
            <span class="nav-label">Cerrar sesión</span>
        </a>

    </nav>

    <!-- Footer: info del paciente -->
    <div class="sidebar-footer">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="user-info">
            <?php $usuario = obtenerUsuario(); ?>
            <div class="user-name"><?= htmlspecialchars($usuario['nombre'] ?? 'Paciente') ?></div>
            <div class="user-role">Paciente · Clínica SI</div>
        </div>
    </div>

</aside>
