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
</head>
<body>

    <?php $paginaActual = 'inicio'; include '../includes/sidebar_admin.php'?>

    <!-- ══════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════ -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-grid-fill" style="color:var(--dorado); margin-right:8px;"></i>Inicio</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <!-- Área de contenido -->
        <div class="content-area">

            <div class="welcome-header">
                <h1>Bienvenido, Admin 👋</h1>
                <p>Resumen general del sistema — Clínica Salud Integral</p>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-value">—</div>
                    <div class="stat-label">Usuarios registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="stat-value">—</div>
                    <div class="stat-label">Doctores activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                    <div class="stat-value">—</div>
                    <div class="stat-label">Citas hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div class="stat-value">—</div>
                    <div class="stat-label">Administradores</div>
                </div>
            </div>

            <!-- Accesos rápidos (se activarán al crear las páginas CRUD) -->
            <h3 class="section-title"><i class="bi bi-lightning-fill"></i> Accesos rápidos</h3>
            <div class="quick-grid">
                <a href="#" class="quick-card" style="opacity:0.5; pointer-events:none;">
                    <span class="qc-icon"><i class="bi bi-person-plus-fill"></i></span>
                    <span class="qc-label">Crear usuario</span>
                </a>
                <a href="#" class="quick-card" style="opacity:0.5; pointer-events:none;">
                    <span class="qc-icon"><i class="bi bi-people-fill"></i></span>
                    <span class="qc-label">Ver usuarios</span>
                </a>
                <a href="#" class="quick-card" style="opacity:0.5; pointer-events:none;">
                    <span class="qc-icon"><i class="bi bi-person-gear"></i></span>
                    <span class="qc-label">Editar usuario</span>
                </a>
                <a href="#" class="quick-card" style="opacity:0.5; pointer-events:none;">
                    <span class="qc-icon"><i class="bi bi-person-x-fill"></i></span>
                    <span class="qc-label">Eliminar usuario</span>
                </a>
            </div>

        </div>
    </main>

</body>
</html>
