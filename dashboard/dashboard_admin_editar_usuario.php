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
    <?php include '../includes/sidebar_admin.php'?>

    <!-- ══════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════ -->
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

            <!-- ══════════════════════════════════════════════════
                 PASO 1 — FORMULARIO DE BÚSQUEDA
                 Este form envía tipoId e idUser por GET a la misma página.
                 En PHP: if(isset($_GET['buscar'])) { ...consulta SQL... }
            ══════════════════════════════════════════════════ -->
            <form class="busqueda-id" method="get" action="editar_usuario.php">

                <div style="width:100%;">
                    <div class="bi-titulo"><i class="bi bi-search"></i> Paso 1 — Buscar usuario</div>
                </div>

                <div class="bi-grupo" style="max-width: 200px;">
                    <label class="form-label" for="busq_tipoId">Tipo de ID</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-card-text"></i>
                        <!--
                            PHP: añade selected al option que coincida con $_GET['busq_tipoId']
                            <option value="cc" <?= ($_GET['busq_tipoId'] ?? '') === 'cc' ? 'selected' : '' ?>>
                        -->
                        <select class="form-select" id="busq_tipoId" name="busq_tipoId" required>
                            <option value="cc">Cédula de ciudadanía</option>
                            <option value="ti">Tarjeta de identidad</option>
                            <option value="ce">Cédula de extranjería</option>
                        </select>
                    </div>
                </div>

                <div class="bi-grupo">
                    <label class="form-label" for="busq_idUser">Número de identificación</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-upc"></i>
                        <!--
                            PHP: value="<?= htmlspecialchars($_GET['busq_idUser'] ?? '') ?>"
                        -->
                        <input type="text" class="form-control" id="busq_idUser" name="busq_idUser"
                               placeholder="Ej: 1234567890" required>
                    </div>
                </div>

                <!-- Campo oculto que le dice al PHP que es una búsqueda -->
                <input type="hidden" name="buscar" value="1">

                <button type="submit" class="btn-primary-clinic" style="padding: 10px 24px;">
                    <i class="bi bi-search"></i> Buscar
                </button>

            </form>

            <!-- ══════════════════════════════════════════════════
                 PASO 2 — FORMULARIO DE EDICIÓN
                 PHP controla dos cosas:
                   1. Si $usuarioEncontrado es false → campos disabled, muestra aviso amarillo
                   2. Si $usuarioEncontrado es true  → campos enabled con value del $row, muestra aviso verde

                 Ejemplo de lógica PHP para esta sección:
                 <?php
                    $usuarioEncontrado = false;
                    $row = [];
                    if (isset($_GET['buscar'])) {
                        $sql = "SELECT * FROM usuario WHERE tipoId = ? AND idUser = ? LIMIT 1";
                        // ...prepared statement...
                        if ($result->num_rows > 0) {
                            $usuarioEncontrado = true;
                            $row = $result->fetch_assoc();
                        }
                    }
                 ?>
            ══════════════════════════════════════════════════ -->

            <div class="seccion-edicion">

                <div class="seccion-edicion-header">
                    <i class="bi bi-pencil-square" style="font-size:1.3rem; color:var(--dorado);"></i>
                    <h2>Paso 2 — Editar datos</h2>
                </div>

                <!--
                    AVISO CUANDO NO SE HA BUSCADO TODAVÍA (o no se encontró el usuario):
                    PHP: muestra este div si !$usuarioEncontrado
                         <div class="aviso-busqueda <?= $usuarioEncontrado ? 'aviso-oculto' : '' ?>">

                    Para la previsualización HTML lo mostramos siempre:
                -->
                <div class="aviso-busqueda" id="avisoBusqueda">
                    <i class="bi bi-info-circle-fill"></i>
                    Busca un usuario en el paso 1 para habilitar este formulario y autocompletar sus datos.
                </div>

                <!--
                    AVISO CUANDO EL USUARIO FUE ENCONTRADO:
                    PHP: muestra este div si $usuarioEncontrado
                         <div class="usuario-encontrado <?= !$usuarioEncontrado ? 'aviso-oculto' : '' ?>">
                              <i class="bi bi-check-circle-fill"></i>
                              Usuario encontrado: <strong><?= htmlspecialchars($row['nameUser'] . ' ' . $row['secondNameUser']) ?></strong>
                              — Edita los campos y guarda los cambios.
                         </div>

                    Para la previsualización HTML lo ocultamos:
                -->
                <div class="usuario-encontrado aviso-oculto" id="avisoEncontrado">
                    <i class="bi bi-check-circle-fill"></i>
                    Usuario encontrado: <strong>Nombre Apellido</strong> — Edita los campos y guarda los cambios.
                </div>

                <!--
                    FORMULARIO DE EDICIÓN
                    PHP: el action apunta al script que hace el UPDATE
                         <form action="../php/editarUserInt.php" method="post">
                    Los value de cada campo se rellenan con $row['campo'] si $usuarioEncontrado.
                    El atributo disabled se añade a cada input si !$usuarioEncontrado:
                         <input ... <?= !$usuarioEncontrado ? 'disabled' : '' ?> value="<?= htmlspecialchars($row['nameUser'] ?? '') ?>">

                    Para la previsualización HTML todos los campos tienen disabled y value vacío.
                -->
                <form action="../php/editarUserInt.php" method="post">

                    <!-- Campo oculto con la ID original para el WHERE del UPDATE -->
                    <!--
                        PHP: <input type="hidden" name="idUser_original" value="<?= htmlspecialchars($row['idUser'] ?? '') ?>">
                             <input type="hidden" name="tipoId_original" value="<?= htmlspecialchars($row['tipoId'] ?? '') ?>">
                    -->
                    <input type="hidden" name="idUser_original"  value="">
                    <input type="hidden" name="tipoId_original"  value="">

                    <!-- Datos personales -->
                    <p class="form-section-label"><i class="bi bi-person"></i> Datos personales</p>
                    <div class="form-grid">

                        <div>
                            <label class="form-label" for="nameUser">Nombre</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <!-- PHP: value="<?= htmlspecialchars($row['nameUser'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="text" class="form-control" id="nameUser"
                                       name="nameUser" placeholder="Nombre" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="secondNameUser">Apellido</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-person-fill"></i>
                                <!-- PHP: value="<?= htmlspecialchars($row['secondNameUser'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="text" class="form-control" id="secondNameUser"
                                       name="secondNameUser" placeholder="Apellido" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="tipoId">Tipo de ID</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-card-text"></i>
                                <!-- PHP: <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <select class="form-select" id="tipoId" name="tipoId" disabled>
                                    <!-- PHP: añade selected según $row['tipoId'] -->
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
                                <!-- PHP: value="<?= htmlspecialchars($row['idUser'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="text" class="form-control" id="idUser"
                                       name="idUser" placeholder="Ej: 1234567890" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="fechaNacimientoUsr">Fecha de nacimiento</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-calendar3"></i>
                                <!-- PHP: value="<?= htmlspecialchars($row['fechaNacimientoUsr'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="date" class="form-control" id="fechaNacimientoUsr"
                                       name="fechaNacimientoUsr" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="generoUser">Género</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-gender-ambiguous"></i>
                                <!-- PHP: <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <select class="form-select" id="generoUser" name="generoUser" disabled>
                                    <!-- PHP: añade selected según $row['generoUser'] -->
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
                                <!-- PHP: value="<?= htmlspecialchars($row['emailUser'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="email" class="form-control" id="emailUser"
                                       name="emailUser" placeholder="correo@ejemplo.com" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="telUser">Teléfono</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-telephone-fill"></i>
                                <!-- PHP: value="<?= htmlspecialchars($row['telUser'] ?? '') ?>" <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="text" class="form-control" id="telUser"
                                       name="telUser" placeholder="Ej: 3001234567" disabled>
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
                                <!-- PHP: <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="password" class="form-control" id="passwordUser"
                                       name="passwordUser" placeholder="Dejar vacío para no cambiar" disabled>
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
                                <!-- PHP: <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <input type="password" class="form-control" id="repeatPasswordUser"
                                       name="repeatPasswordUser" placeholder="Repite la contraseña" disabled>
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
                                <!-- PHP: <?= !$usuarioEncontrado ? 'disabled' : '' ?> -->
                                <select class="form-select" id="rolUser" name="rolUser" disabled>
                                    <!-- PHP: añade selected según $row['rolUser'] -->
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
                        <!--
                            PHP: deshabilita el botón si !$usuarioEncontrado
                            <button type="submit" class="btn-primary-clinic" <?= !$usuarioEncontrado ? 'disabled' : '' ?>>
                        -->
                        <button type="submit" class="btn-primary-clinic" disabled
                                style="opacity: 0.5; cursor: not-allowed;">
                            <i class="bi bi-floppy-fill"></i> Guardar cambios
                        </button>
                        <a href="ver_usuarios.php" class="btn-secondary-clinic">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </main>

</body>
</html>
