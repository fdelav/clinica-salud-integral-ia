<!-- ══════════════════════════════════════
        SIDEBAR / BARRA LATERAL — DOCTOR
        Ruta: includes/sidebar_doctor.php
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

        <a href="../index.php" class="nav-item back-btn" data-tooltip="Ir al inicio">
            <i class="bi bi-house-fill nav-icon"></i>
            <span class="nav-label">Página principal</span>
        </a>

        <!-- Sección: Panel -->
        <span class="nav-section-label">Panel</span>

        <a href="dashboard_doctor.php" class="nav-item <?= $paginaActual === 'inicio' ? 'active' : '' ?>" data-tooltip="Inicio">
            <i class="bi bi-grid-fill nav-icon"></i>
            <span class="nav-label">Inicio</span>
        </a>

        <!-- Sección: Citas -->
        <span class="nav-section-label">Citas</span>

        <a href="dashboard_doctor_citas.php" class="nav-item <?= $paginaActual === 'citas' ? 'active' : '' ?>" data-tooltip="Mis citas">
            <i class="bi bi-calendar2-check-fill nav-icon"></i>
            <span class="nav-label">Mis citas</span>
        </a>

        <!-- Sección: Historias médicas -->
        <span class="nav-section-label">Historias médicas</span>

        <a href="dashboard_doctor_crear_historia.php" class="nav-item <?= $paginaActual === 'crear_historia' ? 'active' : '' ?>" data-tooltip="Nueva historia">
            <i class="bi bi-file-earmark-plus-fill nav-icon"></i>
            <span class="nav-label">Nueva historia</span>
        </a>

        <a href="dashboard_doctor_historias.php" class="nav-item <?= $paginaActual === 'historias' ? 'active' : '' ?>" data-tooltip="Ver historias">
            <i class="bi bi-journal-medical nav-icon"></i>
            <span class="nav-label">Ver historias</span>
        </a>

        <!-- Sección: Cuenta -->
        <span class="nav-section-label">Cuenta</span>

        <a href="../includes/cerrar_sesion.php" class="nav-item" data-tooltip="Cerrar sesión" style="color:#c0392b;">
            <i class="bi bi-box-arrow-left nav-icon"></i>
            <span class="nav-label">Cerrar sesión</span>
        </a>

    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="user-info">
            <?php $usuario = obtenerUsuario(); ?>
            <div class="user-name">Dr. <?= htmlspecialchars($usuario['nombre'] ?? 'Doctor') ?></div>
            <div class="user-role">Doctor · Clínica SI</div>
        </div>
    </div>

</aside>
