/* ══════════════════════════════════════════════════════
    ESTADO GLOBAL
══════════════════════════════════════════════════════ */
let entradasPorPagina = 10;
let paginaActual      = 1;
let filtros           = {};          // { columna: { cond, val, val2? } }
let filasFiltradas    = [];          // filas visibles tras aplicar filtros

const cuerpo = document.getElementById('cuerpoTabla');
const todasLasFilas = Array.from(cuerpo.querySelectorAll('tr'));

/* ══════════════════════════════════════════════════════
    PANEL DE FILTRO: abrir / cerrar
══════════════════════════════════════════════════════ */
function toggleFiltro(id) {
    const panel = document.getElementById(id);
    const estaAbierto = panel.classList.contains('abierto');
    // Cerrar todos
    document.querySelectorAll('.filtro-panel').forEach(p => p.classList.remove('abierto'));
    if (!estaAbierto) panel.classList.add('abierto');
}

// Cerrar paneles al hacer clic fuera
document.addEventListener('click', e => {
    if (!e.target.closest('th')) {
        document.querySelectorAll('.filtro-panel').forEach(p => p.classList.remove('abierto'));
    }
});

// Mostrar/ocultar segundo campo cuando condición edad es "entre"
document.getElementById('cond-edad').addEventListener('change', function() {
    document.getElementById('edad-hasta-wrap').style.display =
        this.value === 'entre' ? 'block' : 'none';
});

/* ══════════════════════════════════════════════════════
    APLICAR FILTRO DE COLUMNA
══════════════════════════════════════════════════════ */
function aplicarFiltro(col) {
    let cond = '', val = '', val2 = '';

    if (col === 'genero') {
        val = document.getElementById('val-genero').value;
        cond = 'igual';
    } else if (col === 'rol') {
        val = document.getElementById('val-rol').value;
        cond = 'igual';
    } else if (col === 'identificacion') {
        val  = document.getElementById('val-identificacion').value.trim();
        val2 = document.getElementById('val-tipo-id').value;
        cond = 'id';
    } else if (col === 'edad') {
        cond = document.getElementById('cond-edad').value;
        val  = document.getElementById('val-edad').value;
        val2 = document.getElementById('val-edad-hasta').value;
    } else {
        cond = document.getElementById('cond-' + col).value;
        val  = document.getElementById('val-' + col).value.trim();
    }

    if (val === '' && val2 === '') {
        delete filtros[col];
    } else {
        filtros[col] = { cond, val, val2 };
    }

    // Marcar botón filtro como activo
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        if (btn.dataset.col === col) {
            btn.classList.toggle('activo', !!filtros[col]);
        }
    });

    document.querySelectorAll('.filtro-panel').forEach(p => p.classList.remove('abierto'));
    paginaActual = 1;
    aplicarTodo();
}

function limpiarFiltroCol(col) {
    delete filtros[col];
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        if (btn.dataset.col === col) btn.classList.remove('activo');
    });
    document.querySelectorAll('.filtro-panel').forEach(p => p.classList.remove('abierto'));
    paginaActual = 1;
    aplicarTodo();
}

/* ══════════════════════════════════════════════════════
    LÓGICA DE FILTRADO DE FILAS
══════════════════════════════════════════════════════ */
function cumpleFiltro(fila, col, f) {
    const data = fila.dataset;
    let valor = '';

    switch (col) {
        case 'nombre':         valor = data.nombre.toLowerCase(); break;
        case 'apellido':       valor = data.apellido.toLowerCase(); break;
        case 'correo':         valor = data.correo.toLowerCase(); break;
        case 'telefono':       valor = data.telefono; break;
        case 'genero':         valor = data.genero; break;
        case 'edad':           valor = parseInt(data.edad); break;
        case 'rol':            valor = data.rol; break;
        case 'identificacion':
            // Filtro combinado tipo+número
            const tipoOk = !f.val2 || data.tipoId === f.val2;
            const numOk  = !f.val  || data.id.includes(f.val);
            return tipoOk && numOk;
    }

    const v = typeof valor === 'string' ? valor : valor;
    const fv = f.val.toLowerCase();

    switch (f.cond) {
        case 'igual':   return typeof v === 'number' ? v === parseInt(fv) : v === fv;
        case 'contiene': return v.includes(fv);
        case 'empieza': return v.startsWith(fv);
        case 'mayor':   return v > parseInt(fv);
        case 'menor':   return v < parseInt(fv);
        case 'entre':   return v >= parseInt(fv) && v <= parseInt(f.val2);
        default: return true;
    }
}

function aplicarTodo() {
    filasFiltradas = todasLasFilas.filter(fila => {
        return Object.entries(filtros).every(([col, f]) => cumpleFiltro(fila, col, f));
    });
    renderChips();
    renderPagina();
    renderPaginacion();
}

/* ══════════════════════════════════════════════════════
    CHIPS DE FILTROS ACTIVOS
══════════════════════════════════════════════════════ */
const nombresCol = {
    nombre: 'Nombre', apellido: 'Apellido', identificacion: 'Identificación',
    genero: 'Género', edad: 'Edad', correo: 'Correo', telefono: 'Teléfono', rol: 'Rol'
};
const tipoLabels   = { cc: 'CC', ti: 'TI', ce: 'CE' };
const generoLabels = { m: 'Masculino', f: 'Femenino', o: 'Otro' };
const rolLabels    = { admin: 'Administrador', doctor: 'Doctor', enfermero: 'Enfermero',
                        secretario: 'Secretario', 'diseñador': 'Diseñador', na: 'Sin rol' };

function renderChips() {
    const cont = document.getElementById('filtrosActivos');
    cont.innerHTML = '';
    Object.entries(filtros).forEach(([col, f]) => {
        let desc = '';
        if (col === 'identificacion') {
            desc = [f.val2 ? tipoLabels[f.val2] : '', f.val].filter(Boolean).join(' · ') || '—';
        } else if (col === 'genero') {
            desc = generoLabels[f.val] || f.val;
        } else if (col === 'rol') {
            desc = rolLabels[f.val] || f.val;
        } else if (f.cond === 'entre') {
            desc = `${f.val} – ${f.val2}`;
        } else {
            const condLabel = { igual:'=', contiene:'contiene', empieza:'empieza por', mayor:'>', menor:'<' };
            desc = `${condLabel[f.cond] || f.cond} "${f.val}"`;
        }
        const chip = document.createElement('span');
        chip.className = 'filtro-chip';
        chip.innerHTML = `<strong>${nombresCol[col]}:</strong> ${desc}
            <button onclick="limpiarFiltroCol('${col}')" title="Quitar filtro">
                <i class="bi bi-x"></i>
            </button>`;
        cont.appendChild(chip);
    });
}

/* ══════════════════════════════════════════════════════
    RENDERIZAR FILAS DE LA PÁGINA ACTUAL
══════════════════════════════════════════════════════ */
function renderPagina() {
    const total    = filasFiltradas.length;
    const desde    = (paginaActual - 1) * entradasPorPagina;
    const hasta    = Math.min(desde + entradasPorPagina, total);
    const filasEnPagina = filasFiltradas.slice(desde, hasta);

    // Ocultar todas las filas
    todasLasFilas.forEach(f => f.style.display = 'none');
    // Mostrar solo las de esta página
    filasEnPagina.forEach(f => f.style.display = '');

    // Actualizar info
    document.getElementById('infoDesde').textContent  = total === 0 ? 0 : desde + 1;
    document.getElementById('infoHasta').textContent  = hasta;
    document.getElementById('infoTotal').textContent  = total;
}

/* ══════════════════════════════════════════════════════
    PAGINACIÓN
══════════════════════════════════════════════════════ */
function totalPaginas() {
    return Math.max(1, Math.ceil(filasFiltradas.length / entradasPorPagina));
}

function renderPaginacion() {
    const total = totalPaginas();
    document.getElementById('paginacionInfo').textContent =
        `Página ${paginaActual} de ${total}`;
    document.getElementById('inputPagina').max = total;

    const cont = document.getElementById('paginacion');
    cont.innerHTML = '';

    // Botón anterior
    const btnAnt = crearPagBtn('<i class="bi bi-chevron-left"></i>', paginaActual > 1,
        () => { paginaActual--; renderPagina(); renderPaginacion(); });
    cont.appendChild(btnAnt);

    // Números de página con elipsis estilo navegador
    const paginas = generarPaginas(paginaActual, total);
    paginas.forEach(p => {
        if (p === '...') {
            const sep = document.createElement('span');
            sep.className = 'pag-separador';
            sep.textContent = '…';
            cont.appendChild(sep);
        } else {
            const btn = crearPagBtn(p, true, () => {
                paginaActual = p; renderPagina(); renderPaginacion();
            });
            if (p === paginaActual) btn.classList.add('activa');
            cont.appendChild(btn);
        }
    });

    // Botón siguiente
    const btnSig = crearPagBtn('<i class="bi bi-chevron-right"></i>', paginaActual < total,
        () => { paginaActual++; renderPagina(); renderPaginacion(); });
    cont.appendChild(btnSig);
}

function crearPagBtn(html, habilitado, fn) {
    const btn = document.createElement('button');
    btn.className = 'pag-btn';
    btn.innerHTML = html;
    if (!habilitado) { btn.disabled = true; }
    else { btn.addEventListener('click', fn); }
    return btn;
}

// Genera array de páginas con elipsis: [1, '...', 4, 5, 6, '...', 12]
function generarPaginas(actual, total) {
    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
    const pags = new Set([1, total, actual, actual-1, actual+1].filter(p => p >= 1 && p <= total));
    const sorted = [...pags].sort((a,b) => a-b);
    const result = [];
    sorted.forEach((p, i) => {
        if (i > 0 && p - sorted[i-1] > 1) result.push('...');
        result.push(p);
    });
    return result
}