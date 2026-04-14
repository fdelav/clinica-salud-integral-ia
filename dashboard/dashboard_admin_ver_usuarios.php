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
    <!-- style temporal mientras adaptamos el css -->
    <style>
        /* ── Variables y reset ── */
        :root {
            --amarillo:       #FFD54A;
            --amarillo-claro: #FFF6BB;
            --amarillo-hover: #f5c800;
            --amarillo-borde: #e0c84a;
            --dorado:         #b89000;
            --sidebar-ancho-lg: 220px;
            --sidebar-ancho-md:  68px;
            --bottombar-h:      64px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Nunito", sans-serif; background: #f7f5ee; display: flex; min-height: 100vh; }
        h1,h2,h3 { font-family: "Montserrat", sans-serif; font-weight: 700; }

        /* ── Sidebar (igual que dashboard) ── */
        .sidebar { width: var(--sidebar-ancho-lg); min-height: 100vh; background: #FFF6BB; border-right: 2px solid var(--amarillo-borde); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; overflow: hidden; }
        .sidebar-brand { padding: 20px 14px 16px; border-bottom: 1px solid var(--amarillo-borde); display: flex; align-items: center; gap: 10px; white-space: nowrap; overflow: hidden; }
        .sidebar-brand img { height: 38px; width: 38px; object-fit: contain; flex-shrink: 0; }
        .sidebar-brand-text { font-family: "Montserrat", sans-serif; font-weight: 800; font-size: 0.82rem; color: #5a4800; line-height: 1.2; }
        .sidebar-nav { flex: 1; padding: 12px 0; display: flex; flex-direction: column; gap: 4px; }
        .nav-section-label { font-family: "Montserrat", sans-serif; font-weight: 700; font-size: 0.65rem; letter-spacing: 0.1em; text-transform: uppercase; color: #b89000; padding: 10px 18px 4px; white-space: nowrap; overflow: hidden; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 18px; margin: 0 8px; border-radius: 12px; cursor: pointer; text-decoration: none; color: #3a2f00; font-weight: 600; font-size: 0.92rem; white-space: nowrap; overflow: hidden; transition: background 0.18s, color 0.18s, transform 0.15s; position: relative; }
        .nav-item:hover { background: var(--amarillo); color: #000; transform: translateX(3px); }
        .nav-item.active { background: var(--amarillo); color: #000; box-shadow: 0 2px 8px rgba(255,213,74,0.4); }
        .nav-item.back-btn { background: var(--amarillo-claro); border: 1.5px solid var(--amarillo-borde); margin: 8px 8px 4px; color: #5a4800; }
        .nav-item.back-btn:hover { background: var(--amarillo); border-color: var(--amarillo-hover); }
        .nav-icon { font-size: 1.2rem; flex-shrink: 0; width: 22px; text-align: center; }
        .sidebar-footer { padding: 14px; border-top: 1px solid var(--amarillo-borde); display: flex; align-items: center; gap: 10px; overflow: hidden; white-space: nowrap; }
        .sidebar-footer .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--amarillo); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; border: 2px solid var(--amarillo-borde); }
        .user-name { font-weight: 700; font-size: 0.85rem; color: #3a2f00; }
        .user-role { font-size: 0.75rem; color: #888; }

        /* ── Layout principal ── */
        .main-content { margin-left: var(--sidebar-ancho-lg); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e8e0c0; padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .topbar h2 { font-size: 1.2rem; color: #3a2f00; }
        .topbar-badge { background: var(--amarillo-claro); border: 1.5px solid var(--amarillo-borde); color: #7a6000; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; font-family: "Montserrat", sans-serif; }
        .content-area { padding: 32px 28px; flex: 1; }
        .page-title { font-size: 1.6rem; color: #3a2f00; margin-bottom: 6px; }
        .page-subtitle { color: #888; font-size: 0.95rem; margin-bottom: 28px; }

        /* ── Responsivo sidebar mediano ── */
        @media (max-width: 991px) and (min-width: 577px) {
            .sidebar { width: var(--sidebar-ancho-md); }
            .sidebar-brand-text, .nav-label, .nav-section-label, .sidebar-footer .user-info { opacity: 0; width: 0; overflow: hidden; }
            .sidebar-brand { justify-content: center; padding: 16px 10px; }
            .nav-item { justify-content: center; padding: 12px 0; margin: 0 6px; }
            .nav-item:hover { transform: none; }
            .nav-item[data-tooltip]:hover::after { content: attr(data-tooltip); position: absolute; left: calc(100% + 10px); top: 50%; transform: translateY(-50%); background: #3a2f00; color: #fff; font-size: 0.8rem; font-family: "Nunito", sans-serif; font-weight: 600; padding: 5px 10px; border-radius: 8px; white-space: nowrap; z-index: 200; pointer-events: none; }
            .nav-item[data-tooltip]:hover::before { content: ''; position: absolute; left: calc(100% + 4px); top: 50%; transform: translateY(-50%); border: 6px solid transparent; border-right-color: #3a2f00; z-index: 200; }
            .sidebar-footer { justify-content: center; padding: 10px; }
            .main-content { margin-left: var(--sidebar-ancho-md); }
        }
        @media (max-width: 576px) {
            .sidebar { width: 100%; min-height: auto; height: var(--bottombar-h); top: auto; bottom: 0; left: 0; right: 0; flex-direction: row; border-right: none; border-top: 2px solid var(--amarillo-borde); }
            .sidebar-brand, .nav-section-label, .sidebar-footer { display: none; }
            .sidebar-nav { flex-direction: row; width: 100%; padding: 0; gap: 0; justify-content: space-around; align-items: center; }
            .nav-item { flex-direction: column; gap: 2px; padding: 6px 10px; margin: 4px 2px; border-radius: 10px; min-width: 52px; text-align: center; justify-content: center; transform: none !important; }
            .nav-item .nav-icon { font-size: 1.3rem; width: auto; }
            .nav-item .nav-label { font-size: 0.62rem; white-space: nowrap; }
            .nav-item.back-btn { margin: 4px 2px; }
            .main-content { margin-left: 0; padding-bottom: var(--bottombar-h); }
            .content-area { padding: 20px 16px; }
            .topbar { padding: 14px 16px; }
        }

        /* ══════════════════════════════════════
           BÚSQUEDA DIRECTA POR IDENTIFICACIÓN
        ══════════════════════════════════════ */
        .busqueda-directa {
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }
        .busqueda-directa .bd-titulo {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--dorado);
            margin-bottom: 8px;
        }
        .busqueda-directa .bd-grupo {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 140px;
        }
        .busqueda-directa label {
            font-family: "Nunito", sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            color: #444;
            margin-bottom: 4px;
        }
        .busqueda-directa select,
        .busqueda-directa input[type="text"] {
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 10px;
            padding: 9px 13px;
            font-family: "Nunito", sans-serif;
            font-size: 0.9rem;
            background: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .busqueda-directa select:focus,
        .busqueda-directa input[type="text"]:focus {
            border-color: var(--amarillo-hover);
            box-shadow: 0 0 0 3px rgba(255,213,74,0.3);
        }
        .busqueda-directa .bd-grupo-id {
            flex: 2;
            min-width: 180px;
        }
        .bd-btn {
            background: var(--amarillo);
            color: #000;
            border: 2px solid var(--amarillo-hover);
            border-radius: 10px;
            padding: 9px 20px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            transition: background 0.18s, transform 0.15s;
            white-space: nowrap;
            align-self: flex-end;
        }
        .bd-btn:hover { background: var(--amarillo-hover); transform: scale(1.02); }
        .bd-btn-limpiar {
            background: none;
            color: #888;
            border: 1.5px solid #ddd;
            border-radius: 10px;
            padding: 9px 16px;
            font-family: "Nunito", sans-serif;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            transition: border-color 0.18s, color 0.18s;
            align-self: flex-end;
            white-space: nowrap;
        }
        .bd-btn-limpiar:hover { border-color: var(--amarillo-borde); color: #444; }

        /* ══════════════════════════════════════
           BARRA SUPERIOR DE LA TABLA
        ══════════════════════════════════════ */
        .tabla-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 14px;
        }
        .tabla-info {
            font-size: 0.88rem;
            color: #888;
        }
        .tabla-info strong { color: #3a2f00; }
        .entradas-selector {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: #555;
        }
        .entradas-selector select {
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 8px;
            padding: 5px 10px;
            font-family: "Nunito", sans-serif;
            font-size: 0.88rem;
            background: #fff;
            cursor: pointer;
            outline: none;
        }
        .entradas-selector select:focus {
            border-color: var(--amarillo-hover);
            box-shadow: 0 0 0 2px rgba(255,213,74,0.3);
        }

        /* ══════════════════════════════════════
           CONTENEDOR DE TABLA (scroll horizontal en móvil)
        ══════════════════════════════════════ */
        .tabla-wrapper {
            background: #fff;
            border-radius: 16px;
            border: 1.5px solid var(--amarillo-borde);
            overflow: hidden;
            overflow-x: auto;
        }

        /* ══════════════════════════════════════
           TABLA
        ══════════════════════════════════════ */
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        /* Encabezados */
        thead tr {
            background: var(--amarillo-claro);
            border-bottom: 2px solid var(--amarillo-borde);
        }
        th {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #5a4800;
            padding: 0;
            white-space: nowrap;
            position: relative;
        }

        /* Celda interna del th: texto + botón filtro */
        .th-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 13px 14px;
        }

        /* Botón de filtro en el encabezado */
        .btn-filtro {
            background: none;
            border: none;
            cursor: pointer;
            color: #b89000;
            font-size: 0.85rem;
            padding: 2px 4px;
            border-radius: 5px;
            line-height: 1;
            flex-shrink: 0;
            transition: background 0.15s, color 0.15s;
        }
        .btn-filtro:hover { background: var(--amarillo); color: #000; }
        .btn-filtro.activo { color: #000; background: var(--amarillo); }

        /* Filas del cuerpo */
        tbody tr {
            border-bottom: 1px solid #f0e8c8;
            transition: background 0.15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fffde7; }
        tbody tr.fila-resaltada { background: #fff9d6; }

        td {
            padding: 12px 14px;
            font-size: 0.9rem;
            color: #333;
            white-space: nowrap;
        }

        /* Badges de tipo ID y género */
        .badge-id {
            display: inline-block;
            background: var(--amarillo-claro);
            border: 1px solid var(--amarillo-borde);
            color: #7a6000;
            font-size: 0.72rem;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            padding: 2px 8px;
            border-radius: 20px;
            margin-right: 5px;
            text-transform: uppercase;
        }
        .badge-genero {
            display: inline-block;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .badge-m { background: #dbeafe; color: #1e40af; }
        .badge-f { background: #fce7f3; color: #9d174d; }
        .badge-o { background: #ede9fe; color: #5b21b6; }

        /* Badges de rol */
        .badge-rol {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: capitalize;
        }
        .rol-admin      { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .rol-doctor     { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .rol-enfermero  { background: #e0f2fe; color: #0c4a6e; border: 1px solid #7dd3fc; }
        .rol-secretario { background: #ede9fe; color: #4c1d95; border: 1px solid #c4b5fd; }
        .rol-disenador  { background: #fce7f3; color: #831843; border: 1px solid #f9a8d4; }
        .rol-na         { background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db; }

        /* Botones editar / eliminar */
        .btn-accion {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 11px;
            border-radius: 8px;
            font-family: "Nunito", sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            border: 1.5px solid transparent;
            text-decoration: none;
            transition: background 0.15s, transform 0.12s;
            white-space: nowrap;
        }
        .btn-accion:hover { transform: scale(1.04); }
        .btn-editar {
            background: var(--amarillo-claro);
            border-color: var(--amarillo-borde);
            color: #5a4800;
        }
        .btn-editar:hover { background: var(--amarillo); }
        .btn-eliminar {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #be123c;
        }
        .btn-eliminar:hover { background: #ffe4e6; }
        .td-acciones { display: flex; gap: 6px; align-items: center; }

        /* ══════════════════════════════════════
           PANEL DE FILTRO (popup por columna)
        ══════════════════════════════════════ */
        .filtro-panel {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            z-index: 300;
            background: #fff;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 12px;
            padding: 14px;
            min-width: 220px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            flex-direction: column;
            gap: 8px;
        }
        .filtro-panel.abierto { display: flex; }

        .filtro-panel label {
            font-family: "Nunito", sans-serif;
            font-weight: 600;
            font-size: 0.8rem;
            color: #555;
        }
        .filtro-panel select,
        .filtro-panel input[type="text"],
        .filtro-panel input[type="number"],
        .filtro-panel input[type="date"] {
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 8px;
            padding: 7px 10px;
            font-family: "Nunito", sans-serif;
            font-size: 0.88rem;
            width: 100%;
            background: #fff;
            outline: none;
        }
        .filtro-panel select:focus,
        .filtro-panel input:focus {
            border-color: var(--amarillo-hover);
            box-shadow: 0 0 0 2px rgba(255,213,74,0.3);
        }
        .filtro-panel-acciones {
            display: flex;
            gap: 6px;
            margin-top: 4px;
        }
        .filtro-panel-acciones .btn-aplicar {
            flex: 1;
            background: var(--amarillo);
            border: 2px solid var(--amarillo-hover);
            border-radius: 8px;
            padding: 7px 0;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .filtro-panel-acciones .btn-aplicar:hover { background: var(--amarillo-hover); }
        .filtro-panel-acciones .btn-limpiar-filtro {
            background: none;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 7px 12px;
            font-family: "Nunito", sans-serif;
            font-weight: 600;
            font-size: 0.82rem;
            color: #888;
            cursor: pointer;
            transition: border-color 0.15s;
        }
        .filtro-panel-acciones .btn-limpiar-filtro:hover { border-color: var(--amarillo-borde); color: #444; }

        /* ══════════════════════════════════════
           FILTROS ACTIVOS (chips bajo la toolbar)
        ══════════════════════════════════════ */
        .filtros-activos {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
            min-height: 0;
        }
        .filtro-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--amarillo-claro);
            border: 1px solid var(--amarillo-borde);
            border-radius: 20px;
            padding: 3px 10px 3px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #5a4800;
        }
        .filtro-chip button {
            background: none;
            border: none;
            cursor: pointer;
            color: #b89000;
            font-size: 0.85rem;
            padding: 0;
            line-height: 1;
        }
        .filtro-chip button:hover { color: #c00; }

        /* ══════════════════════════════════════
           PAGINACIÓN
        ══════════════════════════════════════ */
        .paginacion-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
        }
        .paginacion-info {
            font-size: 0.85rem;
            color: #888;
        }
        .paginacion {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .pag-btn {
            width: 34px;
            height: 34px;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 8px;
            background: #fff;
            color: #5a4800;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, border-color 0.15s;
            text-decoration: none;
        }
        .pag-btn:hover { background: var(--amarillo-claro); border-color: var(--amarillo-hover); }
        .pag-btn.activa { background: var(--amarillo); border-color: var(--amarillo-hover); color: #000; }
        .pag-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .pag-btn.icon { font-size: 1rem; }
        .pag-separador { color: #bbb; font-size: 0.9rem; padding: 0 2px; }

        /* Input ir a página */
        .pag-goto {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #555;
        }
        .pag-goto input[type="number"] {
            width: 52px;
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 8px;
            padding: 5px 8px;
            font-family: "Nunito", sans-serif;
            font-size: 0.85rem;
            text-align: center;
            outline: none;
        }
        .pag-goto input[type="number"]:focus {
            border-color: var(--amarillo-hover);
            box-shadow: 0 0 0 2px rgba(255,213,74,0.3);
        }
        .pag-goto button {
            background: var(--amarillo-claro);
            border: 1.5px solid var(--amarillo-borde);
            border-radius: 8px;
            padding: 5px 10px;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            color: #5a4800;
            transition: background 0.15s;
        }
        .pag-goto button:hover { background: var(--amarillo); }
    </style>
</head>
<body>
    <?php include '../includes/sidebar_admin.php'?>

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
