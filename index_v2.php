<?php
require_once 'includes/sesiones.php';
iniciarSesion();

$usuario  = obtenerUsuario();
$logueado = $usuario !== null;

// Determina la ruta del dashboard según el rol
$dashboardUrl = '#';
if ($logueado) {
    switch ($usuario['rol']) {
        case 'admin':      $dashboardUrl = 'dashboard/dashboard_admin.php';      break;
        case 'doctor':     $dashboardUrl = 'dashboard/dashboard_doctor.php';     break;
        case 'enfermero':  $dashboardUrl = 'dashboard/dashboard_enfermero.php';  break;
        case 'secretario': $dashboardUrl = 'dashboard/dashboard_secretario.php'; break;
        default:           $dashboardUrl = 'dashboard/dashboard_paciente.php';   break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clínica Salud Integral</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="Css/my_style.css" rel="stylesheet">
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNavbar">
        <div class="container-fluid px-3">

            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="Img/logo clinica salud integral.png" class="imagenLogo" alt="Logo Clínica Salud Integral">
            </a>

            <!-- Botón hamburguesa -->
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarMenu"
                    aria-controls="navbarMenu"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links del menú -->
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link menu" href="#sobre_nosotros">Sobre nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu" href="#nuestros_servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu" href="#pedir_cita">Pedir Cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu" href="#ubicanos">Ubícanos</a>
                    </li>

                    <?php if ($logueado): ?>
                        <!-- Usuario logueado: Dashboard + Cerrar Sesión -->
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link menu btn-nav-outline" href="<?= htmlspecialchars($dashboardUrl) ?>">
                                <i class="bi bi-grid-1x2-fill me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link menu btn-nav-danger" href="includes/cerrar_sesion.php">
                                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Sin sesión: Iniciar Sesión + Registrarse -->
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link menu btn-nav-outline" href="Html/login.html">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="nav-link menu btn-nav-filled" href="Html/registro.html">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== CARRUSEL ===== -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="Img/imagen-header.jpg" class="d-block w-100 imagenCarousel" alt="Banner 1">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                    <h2 class="carousel-titulo">Tu salud, nuestra prioridad</h2>
                    <p class="carousel-subtitulo">Atención médica de calidad para toda la familia</p>
                    <a href="#pedir_cita" class="btn btn-carousel mt-2">Pedir Cita</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="Img/doctor-1s.jpg" class="d-block w-100 imagenCarousel" alt="Banner 2">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                    <h2 class="carousel-titulo">Especialistas a tu lado</h2>
                    <p class="carousel-subtitulo">Más de 9 especialidades médicas disponibles</p>
                    <a href="#sobre_nosotros" class="btn btn-carousel mt-2">Conócenos</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="Img/doctor-3.jpg" class="d-block w-100 imagenCarousel" alt="Banner 3">
                <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                    <h2 class="carousel-titulo">Agenda fácil y rápido</h2>
                    <p class="carousel-subtitulo">Por doctor o por fecha, como prefieras</p>
                    <a href="#pedir_cita" class="btn btn-carousel mt-2">Ver Disponibilidad</a>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- ===== SOBRE NOSOTROS ===== -->
    <section class="seccion2" id="sobre_nosotros">
        <article class="articulos2">
            <h1>Sobre nosotros</h1>
            <p class="textoArticulo2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse quis condimentum purus, at fermentum ante. Integer lobortis sem nunc, at vehicula enim tempor in. Morbi sapien ex, bibendum luctus justo et, pulvinar feugiat sapien. Sed et metus ac sem iaculis laoreet a non metus. In hac habitasse platea dictumst. Etiam a urna justo. Nullam in enim porttitor, lobortis libero sed, volutpat odio.</p>
        </article>
        <article class="articulos2">
            <img src="Img/doctor-1s.jpg" class="imagenArticulo" alt="Doctor">
        </article>
    </section>

    <!-- ===== PEDIR CITA ===== -->
    <section id="pedir_cita">
        <h1>Pedir Cita</h1>
        <a href="#sobre_nosotros">
            <article class="articuloBoton">
                <img src="Img/doctor-3.jpg" class="imagenBoton" alt="Por Doctor">
                <p class="textoBoton">Por Doctor</p>
            </article>
        </a>
        <a href="#sobre_nosotros">
            <article class="articuloBoton">
                <img src="Img/calendario.jpg" class="imagenBoton" alt="Por Fecha">
                <p class="textoBoton">Por Fecha</p>
            </article>
        </a>
    </section>

    <!-- ===== NUESTROS SERVICIOS ===== -->
    <section id="nuestros_servicios" class="servicios-section">
        <h1>Nuestros Servicios</h1>
        <p class="servicios-subtitulo">Todo lo que necesitas para cuidar tu salud en un solo lugar</p>
        <div class="servicios-grid">

            <article class="servicio-card">
                <div class="servicio-icono">
                    <i class="bi bi-calendar2-heart-fill"></i>
                </div>
                <h3 class="servicio-nombre">Citas Médicas</h3>
                <p class="servicio-desc">Agenda tu consulta con nuestros especialistas de forma rápida, eligiendo por doctor o por fecha según tu disponibilidad.</p>
                <a href="#pedir_cita" class="servicio-link">Agendar ahora <i class="bi bi-arrow-right"></i></a>
            </article>

            <article class="servicio-card">
                <div class="servicio-icono">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h3 class="servicio-nombre">Urgencias</h3>
                <p class="servicio-desc">Atención inmediata las 24 horas. Nuestro equipo de urgencias está preparado para responder cuando más lo necesitas.</p>
                <a href="#ubicanos" class="servicio-link">Ver ubicación <i class="bi bi-arrow-right"></i></a>
            </article>

            <article class="servicio-card">
                <div class="servicio-icono">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
                <h3 class="servicio-nombre">Exámenes Médicos</h3>
                <p class="servicio-desc">Laboratorio clínico, imagenología y pruebas diagnósticas con resultados precisos y en el menor tiempo posible.</p>
                <a href="#pedir_cita" class="servicio-link">Solicitar examen <i class="bi bi-arrow-right"></i></a>
            </article>

            <article class="servicio-card">
                <div class="servicio-icono">
                    <i class="bi bi-chat-heart-fill"></i>
                </div>
                <h3 class="servicio-nombre">Asesoramiento</h3>
                <p class="servicio-desc">Orientación personalizada en salud preventiva, nutrición y bienestar para que tomes las mejores decisiones para tu vida.</p>
                <a href="#pedir_cita" class="servicio-link">Consultar <i class="bi bi-arrow-right"></i></a>
            </article>

        </div>
    </section>

    <!-- ===== NUESTROS DOCTORES ===== -->
    <section>
        <h1>Nuestros Doctores</h1>
        <div id="doctor_card_section" class="doctorCardContainer">
            <template id="doctor_card_template">
                <article class="cardDocrtor">
                    <img src="Img/doctor-1s.jpg" class="imagenBoton" id="doctor_card_img" alt="Doctor">
                    <div class="doctorCardInfo">
                        <p id="doctor_card_name" class="doctorNombre">Doctor Mauricio Zanches</p>
                        <p id="doctor_card_especialidad" class="doctorEspecialidad">Especialidad</p>
                    </div>
                </article>
            </template>
        </div>
    </section>

    <!-- ===== UBÍCANOS ===== -->
    <section class="seccion2" id="ubicanos">
        <article class="articulos2">
            <h1>Ubícanos</h1>
            <p><i class="bi bi-geo-alt-fill"></i> Calle 100 #16b-45, Barrio el Resplandor</p>
            <p><i class="bi bi-telephone-fill"></i> 3102481289</p>
            <p><i class="bi bi-clock-fill"></i> Todos los días de 7am a 8pm</p>
        </article>
        <article class="articulos2">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.6266796120294!2d-74.0577293741325!3d4.660475295314375!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9a433607e0cd%3A0x7f49014fe7203f21!2sUniversidad%20Ean!5e0!3m2!1ses-419!2sco!4v1770135874207!5m2!1ses-419!2sco"
                    style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </article>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer>
        <article>
            <img src="Img/logo clinica salud integral.png" width="100px" alt="Logo">
            <p>Salud para todo momento</p>
        </article>
        <article class="ariculosLinks">
            <div class="divLinks">
                <h3>Encabezado</h3>
                <p>link</p><p>link</p><p>link</p><p>link</p><p>link</p><p>link</p>
            </div>
            <div class="divLinks">
                <h3>Encabezado</h3>
                <p>link</p><p>link</p><p>link</p><p>link</p><p>link</p><p>link</p>
            </div>
            <div class="divLinks">
                <h3>Encabezado</h3>
                <p>link</p><p>link</p><p>link</p><p>link</p><p>link</p><p>link</p>
            </div>
        </article>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Js/my_script.js"></script>
</body>
</html>