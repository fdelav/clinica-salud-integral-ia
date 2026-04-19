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
    <link href="../Css/ver_usuario.css" rel="stylesheet">
</head>
<body>
    <?php $paginaActual = 'ver_usuarios'; include '../includes/sidebar_admin.php'?>

    <!-- ══════════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════════ -->
    <main class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <h2><i class="bi bi-people-fill" style="color:var(--dorado); margin-right:8px;"></i>Ver Usuarios</h2>
            <div class="topbar-right">
                <span class="topbar-badge"><i class="bi bi-shield-fill-check me-1"></i>Administrador</span>
            </div>
        </div>

        <!-- Área de contenido -->
        <div class="content-area">
            <h1 class="page-title">Usuarios del sistema</h1>
            <p class="page-subtitle">Consulta, filtra y gestiona todos los usuarios registrados</p>

            <!-- ══ BÚSQUEDA DIRECTA POR IDENTIFICACIÓN ══
                 En PHP: action="ver_usuarios.php" con $_GET['busq_tipo'] y $_GET['busq_id']
            -->
            <form class="busqueda-directa" method="get" action="ver_usuarios.php" id="formBusquedaDirecta">
                <div style="width:100%;">
                    <div class="bd-titulo"><i class="bi bi-search"></i> Búsqueda directa por identificación</div>
                </div>
                <div class="bd-grupo" style="max-width:180px;">
                    <label for="busq_tipo">Tipo de ID</label>
                    <select name="busq_tipo" id="busq_tipo">
                        <option value="">Todos</option>
                        <option value="cc">Cédula de ciudadanía</option>
                        <option value="ti">Tarjeta de identidad</option>
                        <option value="ce">Cédula de extranjería</option>
                    </select>
                </div>
                <div class="bd-grupo bd-grupo-id">
                    <label for="busq_id">Número de identificación</label>
                    <input type="text" name="busq_id" id="busq_id" placeholder="Ej: 1234567890">
                </div>
                <button type="submit" class="bd-btn">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <button type="button" class="bd-btn-limpiar" onclick="limpiarBusqueda()">
                    <i class="bi bi-x"></i> Limpiar
                </button>
            </form>

            <!-- ══ TOOLBAR DE LA TABLA ══ -->
            <div class="tabla-toolbar">
                <div class="tabla-info">
                    Mostrando <strong id="infoDesde">1</strong>–<strong id="infoHasta">10</strong>
                    de <strong id="infoTotal">0</strong> usuarios
                </div>
                <div class="entradas-selector">
                    Mostrar
                    <select id="selectEntradas" onchange="cambiarEntradas(this.value)">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    por página
                </div>
            </div>

            <!-- Chips de filtros activos -->
            <div class="filtros-activos" id="filtrosActivos"></div>

            <!-- ══ TABLA ══ -->
            <div class="tabla-wrapper">
                <table id="tablaUsuarios">
                    <thead>
                        <tr>
                            <!-- Cada th tiene posición relative para que el panel flotante se posicione bien -->
                            <th style="position:relative;">
                                <div class="th-inner">
                                    Nombre
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-nombre')"
                                            data-col="nombre" title="Filtrar por nombre">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <!-- Panel filtro: texto -->
                                <div class="filtro-panel" id="filtro-nombre">
                                    <label>Condición</label>
                                    <select id="cond-nombre">
                                        <option value="contiene">Contiene</option>
                                        <option value="igual">Igual a</option>
                                        <option value="empieza">Empieza por</option>
                                    </select>
                                    <label>Valor</label>
                                    <input type="text" id="val-nombre" placeholder="Ej: Carlos">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('nombre')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('nombre')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Apellido
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-apellido')"
                                            data-col="apellido" title="Filtrar por apellido">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-apellido">
                                    <label>Condición</label>
                                    <select id="cond-apellido">
                                        <option value="contiene">Contiene</option>
                                        <option value="igual">Igual a</option>
                                        <option value="empieza">Empieza por</option>
                                    </select>
                                    <label>Valor</label>
                                    <input type="text" id="val-apellido" placeholder="Ej: García">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('apellido')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('apellido')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Identificación
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-identificacion')"
                                            data-col="identificacion" title="Filtrar por identificación">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-identificacion">
                                    <label>Tipo de ID</label>
                                    <select id="val-tipo-id">
                                        <option value="">Todos</option>
                                        <option value="cc">Cédula de ciudadanía</option>
                                        <option value="ti">Tarjeta de identidad</option>
                                        <option value="ce">Cédula de extranjería</option>
                                    </select>
                                    <label>Número</label>
                                    <input type="text" id="val-identificacion" placeholder="Ej: 1234567890">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('identificacion')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('identificacion')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Género
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-genero')"
                                            data-col="genero" title="Filtrar por género">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-genero">
                                    <label>Género</label>
                                    <select id="val-genero">
                                        <option value="">Todos</option>
                                        <option value="m">Masculino</option>
                                        <option value="f">Femenino</option>
                                        <option value="o">Otro</option>
                                    </select>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('genero')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('genero')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Edad
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-edad')"
                                            data-col="edad" title="Filtrar por edad">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-edad">
                                    <label>Condición</label>
                                    <select id="cond-edad">
                                        <option value="igual">Igual a</option>
                                        <option value="mayor">Mayor que</option>
                                        <option value="menor">Menor que</option>
                                        <option value="entre">Entre</option>
                                    </select>
                                    <label>Valor</label>
                                    <input type="number" id="val-edad" placeholder="Ej: 30" min="0" max="120">
                                    <span id="edad-hasta-wrap" style="display:none;">
                                        <label>Hasta</label>
                                        <input type="number" id="val-edad-hasta" placeholder="Ej: 50" min="0" max="120">
                                    </span>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('edad')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('edad')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Correo
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-correo')"
                                            data-col="correo" title="Filtrar por correo">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-correo">
                                    <label>Condición</label>
                                    <select id="cond-correo">
                                        <option value="contiene">Contiene</option>
                                        <option value="igual">Igual a</option>
                                        <option value="empieza">Empieza por</option>
                                    </select>
                                    <label>Valor</label>
                                    <input type="text" id="val-correo" placeholder="Ej: gmail.com">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('correo')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('correo')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Teléfono
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-telefono')"
                                            data-col="telefono" title="Filtrar por teléfono">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-telefono">
                                    <label>Condición</label>
                                    <select id="cond-telefono">
                                        <option value="contiene">Contiene</option>
                                        <option value="empieza">Empieza por</option>
                                    </select>
                                    <label>Valor</label>
                                    <input type="text" id="val-telefono" placeholder="Ej: 310">
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('telefono')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('telefono')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th style="position:relative;">
                                <div class="th-inner">
                                    Rol
                                    <button class="btn-filtro" onclick="toggleFiltro('filtro-rol')"
                                            data-col="rol" title="Filtrar por rol">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                                <div class="filtro-panel" id="filtro-rol">
                                    <label>Rol</label>
                                    <select id="val-rol">
                                        <option value="">Todos</option>
                                        <option value="admin">Administrador</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="enfermero">Enfermero</option>
                                        <option value="secretario">Secretario</option>
                                        <option value="diseñador">Diseñador</option>
                                        <option value="na">Sin rol</option>
                                    </select>
                                    <div class="filtro-panel-acciones">
                                        <button class="btn-aplicar" onclick="aplicarFiltro('rol')">Aplicar</button>
                                        <button class="btn-limpiar-filtro" onclick="limpiarFiltroCol('rol')">Limpiar</button>
                                    </div>
                                </div>
                            </th>

                            <th>
                                <div class="th-inner">Acciones</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">

                        <!-- Filas de demo para previsualización -->
                        <tr data-nombre="Carlos" data-apellido="Mendoza" data-tipo-id="cc" data-id="1020304050" data-genero="m" data-edad="34" data-correo="carlos@gmail.com" data-telefono="3101234567" data-rol="doctor">
                            <td>Carlos</td>
                            <td>Mendoza</td>
                            <td><span class="badge-id">CC</span>1020304050</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>34</td>
                            <td>carlos@gmail.com</td>
                            <td>3101234567</td>
                            <td><span class="badge-rol rol-doctor">Doctor</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=1020304050" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=1020304050" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Carlos Mendoza?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="María" data-apellido="García" data-tipo-id="cc" data-id="1030405060" data-genero="f" data-edad="28" data-correo="maria@hotmail.com" data-telefono="3209876543" data-rol="enfermero">
                            <td>María</td>
                            <td>García</td>
                            <td><span class="badge-id">CC</span>1030405060</td>
                            <td><span class="badge-genero badge-f">Femenino</span></td>
                            <td>28</td>
                            <td>maria@hotmail.com</td>
                            <td>3209876543</td>
                            <td><span class="badge-rol rol-enfermero">Enfermero</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=1030405060" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=1030405060" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a María García?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Juan" data-apellido="Rodríguez" data-tipo-id="ti" data-id="1005006007" data-genero="m" data-edad="17" data-correo="juan@gmail.com" data-telefono="3156667778" data-rol="na">
                            <td>Juan</td>
                            <td>Rodríguez</td>
                            <td><span class="badge-id">TI</span>1005006007</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>17</td>
                            <td>juan@gmail.com</td>
                            <td>3156667778</td>
                            <td><span class="badge-rol rol-na">Sin rol</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=1005006007" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=1005006007" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Juan Rodríguez?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Ana" data-apellido="Martínez" data-tipo-id="ce" data-id="987654321" data-genero="f" data-edad="41" data-correo="ana@empresa.com" data-telefono="3012223334" data-rol="secretario">
                            <td>Ana</td>
                            <td>Martínez</td>
                            <td><span class="badge-id">CE</span>987654321</td>
                            <td><span class="badge-genero badge-f">Femenino</span></td>
                            <td>41</td>
                            <td>ana@empresa.com</td>
                            <td>3012223334</td>
                            <td><span class="badge-rol rol-secretario">Secretario</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=987654321" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=987654321" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Ana Martínez?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Roberto" data-apellido="López" data-tipo-id="cc" data-id="1111222333" data-genero="m" data-edad="55" data-correo="roberto@gmail.com" data-telefono="3184445556" data-rol="admin">
                            <td>Roberto</td>
                            <td>López</td>
                            <td><span class="badge-id">CC</span>1111222333</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>55</td>
                            <td>roberto@gmail.com</td>
                            <td>3184445556</td>
                            <td><span class="badge-rol rol-admin">Administrador</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=1111222333" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=1111222333" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Roberto López?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Sandra" data-apellido="Fernández" data-tipo-id="cc" data-id="2223334445" data-genero="f" data-edad="38" data-correo="sandra@clinica.com" data-telefono="3117778889" data-rol="secretario">
                            <td>Sandra</td>
                            <td>Fernández</td>
                            <td><span class="badge-id">CC</span>2223334445</td>
                            <td><span class="badge-genero badge-f">Femenino</span></td>
                            <td>38</td>
                            <td>sandra@clinica.com</td>
                            <td>3117778889</td>
                            <td><span class="badge-rol rol-secretario">Secretario</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=2223334445" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=2223334445" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Sandra Fernández?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Miguel" data-apellido="Sánchez" data-tipo-id="cc" data-id="3334445556" data-genero="m" data-edad="46" data-correo="miguel@outlook.com" data-telefono="3008889990" data-rol="doctor">
                            <td>Miguel</td>
                            <td>Sánchez</td>
                            <td><span class="badge-id">CC</span>3334445556</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>46</td>
                            <td>miguel@outlook.com</td>
                            <td>3008889990</td>
                            <td><span class="badge-rol rol-doctor">Doctor</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=3334445556" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=3334445556" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Miguel Sánchez?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Laura" data-apellido="Díaz" data-tipo-id="ti" data-id="1009008007" data-genero="f" data-edad="16" data-correo="laura@gmail.com" data-telefono="3141112223" data-rol="na">
                            <td>Laura</td>
                            <td>Díaz</td>
                            <td><span class="badge-id">TI</span>1009008007</td>
                            <td><span class="badge-genero badge-f">Femenino</span></td>
                            <td>16</td>
                            <td>laura@gmail.com</td>
                            <td>3141112223</td>
                            <td><span class="badge-rol rol-na">Sin rol</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=1009008007" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=1009008007" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Laura Díaz?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Fernando" data-apellido="Ruiz" data-tipo-id="cc" data-id="4445556667" data-genero="m" data-edad="62" data-correo="fernando@yahoo.com" data-telefono="3193334445" data-rol="doctor">
                            <td>Fernando</td>
                            <td>Ruiz</td>
                            <td><span class="badge-id">CC</span>4445556667</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>62</td>
                            <td>fernando@yahoo.com</td>
                            <td>3193334445</td>
                            <td><span class="badge-rol rol-doctor">Doctor</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=4445556667" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=4445556667" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Fernando Ruiz?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Valentina" data-apellido="Torres" data-tipo-id="cc" data-id="5556667778" data-genero="f" data-edad="23" data-correo="vale@gmail.com" data-telefono="3165556667" data-rol="disenador">
                            <td>Valentina</td>
                            <td>Torres</td>
                            <td><span class="badge-id">CC</span>5556667778</td>
                            <td><span class="badge-genero badge-f">Femenino</span></td>
                            <td>23</td>
                            <td>vale@gmail.com</td>
                            <td>3165556667</td>
                            <td><span class="badge-rol rol-disenador">Diseñador</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=5556667778" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=5556667778" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Valentina Torres?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Andrés" data-apellido="Gómez" data-tipo-id="cc" data-id="6667778889" data-genero="m" data-edad="31" data-correo="andres@empresa.co" data-telefono="3127778889" data-rol="enfermero">
                            <td>Andrés</td>
                            <td>Gómez</td>
                            <td><span class="badge-id">CC</span>6667778889</td>
                            <td><span class="badge-genero badge-m">Masculino</span></td>
                            <td>31</td>
                            <td>andres@empresa.co</td>
                            <td>3127778889</td>
                            <td><span class="badge-rol rol-enfermero">Enfermero</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=6667778889" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=6667778889" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Andrés Gómez?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                        <tr data-nombre="Sofía" data-apellido="Herrera" data-tipo-id="ce" data-id="111222333" data-genero="o" data-edad="29" data-correo="sofia@correo.com" data-telefono="3049990001" data-rol="na">
                            <td>Sofía</td>
                            <td>Herrera</td>
                            <td><span class="badge-id">CE</span>111222333</td>
                            <td><span class="badge-genero badge-o">Otro</span></td>
                            <td>29</td>
                            <td>sofia@correo.com</td>
                            <td>3049990001</td>
                            <td><span class="badge-rol rol-na">Sin rol</span></td>
                            <td><div class="td-acciones">
                                <a href="editar_usuario.php?id=111222333" class="btn-accion btn-editar"><i class="bi bi-pencil-fill"></i> Editar</a>
                                <a href="eliminar_usuario.php?id=111222333" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar a Sofía Herrera?')"><i class="bi bi-trash-fill"></i> Eliminar</a>
                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ══ PAGINACIÓN ══ -->
            <div class="paginacion-wrapper">
                <div class="paginacion-info" id="paginacionInfo">Página 1 de 1</div>
                <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                    <div class="paginacion" id="paginacion"></div>
                    <div class="pag-goto">
                        Ir a página
                        <input type="number" id="inputPagina" min="1" value="1">
                        <button onclick="irAPagina()">Ir</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../Js/ver_usuario.js"></script>
</body>
</html>
