<?php
    require_once '../includes/sesiones.php';
    iniciarSesion();

// Si ya hay sesión activa, redirigir directo al index
if (obtenerUsuario() !== null) {
    header('Location: ../index.php');
    exit;
}

// Leer y limpiar el mensaje flash de error (si existe)
$errorLogin = $_SESSION['error_login'] ?? null;
unset($_SESSION['error_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión — Clínica Salud Integral</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../Css/my_style.css" rel="stylesheet">
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="../index.php">
                <img src="../Img/logo clinica salud integral.png" class="imagenLogo" alt="Logo Clínica Salud Integral">
            </a>
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                    aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link menu" href="../index.php#sobre_nosotros">Sobre nosotros</a></li>
                    <li class="nav-item"><a class="nav-link menu" href="../index.php#nuestros_servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link menu" href="../index.php#pedir_cita">Pedir Cita</a></li>
                    <li class="nav-item"><a class="nav-link menu" href="../index.php#ubicanos">Ubícanos</a></li>
                    <li class="nav-item ms-lg-2"><a class="nav-link menu btn-nav-outline" href="login.php">Iniciar Sesión</a></li>
                    <li class="nav-item ms-lg-2"><a class="nav-link menu btn-nav-filled" href="registro.html">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== FORMULARIO ===== -->
    <div class="auth-bg">
        <div class="auth-card" style="max-width: 440px;">

            <img src="../Img/logo clinica salud integral.png" class="auth-logo" alt="Logo">
            <h1>Bienvenido</h1>
            <p class="auth-subtitle">Inicia sesión para gestionar tus citas</p>

            <!-- Alerta de error: visible solo si PHP dejó un flash message -->
            <?php if ($errorLogin): ?>
            <div class="alert alert-danger alert-login" role="alert" style="display:block;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($errorLogin) ?>
            </div>
            <?php endif; ?>

            <form action="../Php/autentificacion.php" method="post">

                <div class="mb-3">
                    <label class="form-label" for="emailUser">Correo electrónico</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" class="form-control" id="emailUser"
                               name="emailUser" placeholder="correo@ejemplo.com"
                               required autocomplete="email">
                    </div>
                </div>

                <div class="mb-1">
                    <label class="form-label" for="passwordUser">
                        Contraseña
                        <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </label>
                    <div class="password-wrap input-icon-wrap">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" class="form-control" id="passwordUser"
                               name="passwordUser" placeholder="Tu contraseña"
                               required autocomplete="current-password">
                        <button type="button" class="btn-toggle-pass" onclick="togglePass('passwordUser', this)">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                    <label class="form-check-label" for="rememberMe"
                           style="font-size:0.9rem; color:#555;">
                        Recordar mi sesión
                    </label>
                </div>

                <button type="submit" class="btn-primary-clinic mt-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                </button>

            </form>

            <div class="auth-divider">o</div>

            <p class="auth-footer-link">
                ¿No tienes cuenta? <a href="registro.html">Regístrate gratis</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        }
    </script>
</body>
</html>
