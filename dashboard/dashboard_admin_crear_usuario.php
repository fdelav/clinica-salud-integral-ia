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
    <?php $paginaActual = 'crear_usuario'; include '../includes/sidebar_admin.php'?>

    <!-- ══════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════ -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-person-plus-fill" style="color:var(--dorado); margin-right:8px;"></i>Crear Usuario</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <!-- Área de contenido -->
        <div class="content-area">
            <h1 class="page-title">Nuevo usuario</h1>
            <p class="page-subtitle">Completa los campos para registrar un usuario en el sistema</p>

            <form action="../php/registroUserInt.php" method="post">

                <!-- Datos personales -->
                <p class="form-section-label"><i class="bi bi-person"></i> Datos personales</p>
                <div class="form-grid">

                    <div>
                        <label class="form-label" for="nameUser">Nombre</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" class="form-control" id="nameUser"
                                   name="nameUser" placeholder="Nombre" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="secondNameUser">Apellido</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" class="form-control" id="secondNameUser"
                                   name="secondNameUser" placeholder="Apellido" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="tipoId">Tipo de ID</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-card-text"></i>
                            <select class="form-select" id="tipoId" name="tipoId" required>
                                <option value="cc">Cédula de ciudadanía</option>
                                <option value="ti">Tarjeta de identidad</option>
                                <option value="ce">Cédula de extranjería</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="idUser">Número de identificación</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-upc"></i>
                            <input type="text" class="form-control" id="idUser"
                                   name="idUser" placeholder="Ej: 1234567890" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="fechaNacimientoUsr">Fecha de nacimiento</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-calendar3"></i>
                            <input type="date" class="form-control" id="fechaNacimientoUsr"
                                   name="fechaNacimientoUsr" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="generoUser">Género</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-gender-ambiguous"></i>
                            <select class="form-select" id="generoUser" name="generoUser" required>
                                <option value="m">Masculino</option>
                                <option value="f">Femenino</option>
                                <option value="o">Otro</option>
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
                            <input type="email" class="form-control" id="emailUser"
                                   name="emailUser" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="telUser">Teléfono</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone-fill"></i>
                            <input type="text" class="form-control" id="telUser"
                                   name="telUser" placeholder="Ej: 3001234567" required>
                        </div>
                    </div>

                </div>

                <!-- Seguridad -->
                <p class="form-section-label"><i class="bi bi-lock"></i> Seguridad</p>
                <div class="form-grid">

                    <div>
                        <label class="form-label" for="passwordUser">Contraseña</label>
                        <div class="password-wrap input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" class="form-control" id="passwordUser"
                                   name="passwordUser" placeholder="Mínimo 8 caracteres" required>
                            <button type="button" class="btn-toggle-pass" onclick="togglePass('passwordUser', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="repeatPasswordUser">Repetir contraseña</label>
                        <div class="password-wrap input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" class="form-control" id="repeatPasswordUser"
                                   name="repeatPasswordUser" placeholder="Repite la contraseña" required>
                            <button type="button" class="btn-toggle-pass" onclick="togglePass('repeatPasswordUser', this)">
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
                            <select class="form-select" id="rolUser" name="rolUser" required>
                                <option value="na">Sin rol</option>
                                <option value="doctor">Doctor</option>
                                <option value="enfermero">Enfermero</option>
                                <option value="secretario">Secretario</option>
                                <option value="diseñador">Diseñador</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Acciones -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary-clinic">
                        <i class="bi bi-check-circle me-2"></i>Crear usuario
                    </button>
                    <a href="dashboard_admin.php" class="btn-secondary-clinic">Cancelar</a>
                </div>

            </form>
        </div>
    </main>

</body>
</html>
