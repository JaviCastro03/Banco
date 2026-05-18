const CONTROLLER = 'index.php';

let todosLosUsuarios   = [];
let todasLasCuentas    = [];
let todosLosMovimientos = [];

/* ══════════════════════════════════════════
   NAVEGACIÓN
══════════════════════════════════════════ */
function showAdminSection(name, el) {
    document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

    document.getElementById('sec-' + name).classList.add('active');

    if (el) {
        el.classList.add('active');
    } else {
        const found = document.querySelector('[data-section="' + name + '"]');
        if (found) found.classList.add('active');
    }

    const titulos = {
        resumen:      'Resumen <span>/ Panel de administración</span>',
        usuarios:     'Usuarios <span>/ Gestión de cuentas</span>',
        cuentas:      'Cuentas <span>/ Bancarias</span>',
        movimientos:  'Movimientos <span>/ Todas las transacciones</span>'
    };
    document.getElementById('topbarTitle').innerHTML = titulos[name] || name;

    if (name === 'resumen')     cargarResumenAdmin();
    if (name === 'usuarios')    cargarUsuariosAdmin();
    if (name === 'cuentas')     cargarCuentasAdmin();
    if (name === 'movimientos') cargarMovimientosAdmin();

    document.getElementById('sidebar').classList.remove('open');
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

/* ══════════════════════════════════════════
   AJAX HELPER
══════════════════════════════════════════ */
function ajax(data, cb) {
    data.ajax = 1;
    fetch(CONTROLLER, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: Object.entries(data).map(([k, v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v)).join('&')
    }).then(r => r.json()).then(cb).catch(e => console.error(e));
}

/* ══════════════════════════════════════════
   FORMATO
══════════════════════════════════════════ */
function formatEur(n) {
    return '€ ' + parseFloat(n || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatFecha(f) {
    if (!f) return '—';
    const d = new Date(f);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' })
        + ' · ' + d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

function htmlE(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function iniAvatar(nombre) {
    return (nombre || 'U').trim().charAt(0).toUpperCase();
}

function rolLabel(rol) {
    const labels = {
        0: '<span class="rol-badge rol-noverif"><i class="bi bi-hourglass-split"></i> No verificado</span>',
        1: '<span class="rol-badge rol-usuario"><i class="bi bi-person-fill"></i> Usuario</span>',
        2: '<span class="rol-badge rol-admin"><i class="bi bi-shield-fill-check"></i> Administrador</span>'
    };
    return labels[rol] || '<span class="rol-badge rol-noverif">Desconocido</span>';
}

/* ══════════════════════════════════════════
   ALERTS
══════════════════════════════════════════ */
function showAdminAlert(id, msg, type = 'success') {
    const icons = { success: 'check-circle-fill', error: 'exclamation-triangle-fill', info: 'info-circle-fill' };
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = `<div class="eco-alert eco-alert-${type}"><i class="bi bi-${icons[type]}"></i><span>${msg}</span></div>`;
    if (type === 'success') setTimeout(() => { if (el) el.innerHTML = ''; }, 4000);
}

/* ══════════════════════════════════════════
   MODALES
══════════════════════════════════════════ */
function abrirModalAdmin(id) {
    document.getElementById(id).classList.add('open');
}

function cerrarModalAdmin(id) {
    document.getElementById(id).classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => {
        if (e.target === m) m.classList.remove('open');
    });
});

/* ══════════════════════════════════════════
   SECCIÓN: RESUMEN
══════════════════════════════════════════ */
function cargarResumenAdmin() {
    ajax({ accion: 'adminResumen' }, r => {
        if (!r.ok) return;
        const d = r.datos;

        document.getElementById('adminStatUsuarios').textContent    = d.total_usuarios || 0;
        document.getElementById('adminStatVerificados').textContent = (d.usuarios_verificados || 0) + ' verificados';
        document.getElementById('adminStatCuentas').textContent     = d.total_cuentas || 0;
        document.getElementById('adminStatTransacciones').textContent = d.total_transacciones || 0;
        document.getElementById('adminStatTransMes').textContent    = (d.trans_este_mes || 0) + ' este mes';

        const ul = document.getElementById('adminUltimosUsuarios');
        if (!d.ultimos_usuarios || !d.ultimos_usuarios.length) {
            ul.innerHTML = '<li class="mov-item"><div style="color:var(--eco-muted);font-size:13px">Sin usuarios registrados.</div></li>';
            return;
        }
        ul.innerHTML = d.ultimos_usuarios.map(u => {
            const nombre = htmlE((u.nombre || '') + ' ' + (u.apellidos || ''));
            const av = iniAvatar(u.nombre);
            return `<li class="mov-item">
                <div class="admin-user-avatar" style="width:36px;height:36px;font-size:13px">${av}</div>
                <div class="mov-info">
                    <div class="mov-name">${nombre}</div>
                    <div class="mov-date">${htmlE(u.email || '')}</div>
                </div>
                ${rolLabel(u.rol)}
            </li>`;
        }).join('');
    });
}

/* ══════════════════════════════════════════
   SECCIÓN: USUARIOS
══════════════════════════════════════════ */
function cargarUsuariosAdmin(busqueda = '') {
    ajax({ accion: 'buscar', busqueda }, data => {
        todosLosUsuarios = data || [];
        document.getElementById('adminUsuariosCount').textContent = todosLosUsuarios.length + ' usuario' + (todosLosUsuarios.length !== 1 ? 's' : '');
        renderTablaUsuarios(todosLosUsuarios);
    });
}

function buscarUsuariosAdmin(val) {
    cargarUsuariosAdmin(val);
}

function renderTablaUsuarios(data) {
    const tbody = document.getElementById('tablaAdminUsuariosTbody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--eco-muted)">No se encontraron usuarios.</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(u => {
        const nombre = htmlE((u.nombre || '').trim());
        const apellidos = htmlE((u.apellidos || '').trim());
        const av = iniAvatar(u.nombre);
        const direccion = [u.via, u.numero_piso, u.municipio, u.provincia].filter(Boolean).join(', ');
        return `<tr>
            <td style="font-size:12px;color:var(--eco-muted);font-weight:600">#${u.id}</td>
            <td>
                <div class="admin-user-cell">
                    <div class="admin-user-avatar">${av}</div>
                    <div>
                        <div class="admin-user-name">${nombre} ${apellidos}</div>
                        <div class="admin-user-id">${htmlE(u.email || '')}</div>
                    </div>
                </div>
            </td>
            <td style="font-size:13px">${htmlE(u.email || '—')}</td>
            <td style="font-size:13px">${htmlE(u.telefono || '—')}</td>
            <td class="addr-cell">${htmlE(direccion || '—')}</td>
            <td>${rolLabel(u.rol)}</td>
            <td>
                <div class="admin-acciones">
                    <button class="btn-accion info" title="Ver movimientos" onclick="abrirHistorialUsuario(${u.id}, '${nombre} ${apellidos}')">
                        <i class="bi bi-clock-history"></i>
                    </button>
                    <button class="btn-accion" title="Editar usuario" onclick="abrirEdicionUsuario(${u.id}, '${nombre}', '${apellidos}', '${htmlE(u.email)}', '${htmlE(u.telefono || '')}', ${u.rol})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn-accion danger" title="Eliminar usuario" onclick="abrirEliminarUsuario(${u.id})">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

/* ── EDITAR USUARIO ── */
function abrirEdicionUsuario(id, nombre, apellidos, email, telefono, rol) {
    document.getElementById('editUserId').value    = id;
    document.getElementById('editNombre').value    = nombre;
    document.getElementById('editApellidos').value = apellidos;
    document.getElementById('editEmail').value     = email;
    document.getElementById('editTelefono').value  = telefono;
    document.getElementById('editRol').value       = rol;
    document.getElementById('alertEditarUsuario').innerHTML = '';
    abrirModalAdmin('modalEditarUsuario');
}

function guardarEdicionUsuario() {
    const id        = document.getElementById('editUserId').value;
    const nombre    = document.getElementById('editNombre').value.trim();
    const apellidos = document.getElementById('editApellidos').value.trim();
    const email     = document.getElementById('editEmail').value.trim();
    const telefono  = document.getElementById('editTelefono').value.trim();
    const rol       = document.getElementById('editRol').value;

    if (!nombre || !email) {
        showAdminAlert('alertEditarUsuario', 'El nombre y el email son obligatorios.', 'error');
        return;
    }

    ajax({ accion: 'actualizar', id, nombre, apellidos, email, telefono, rol }, r => {
        if (r.ok) {
            showAdminAlert('alertEditarUsuario', 'Usuario actualizado correctamente.', 'success');
            setTimeout(() => {
                cerrarModalAdmin('modalEditarUsuario');
                cargarUsuariosAdmin(document.getElementById('adminBuscador').value);
            }, 1200);
        } else {
            showAdminAlert('alertEditarUsuario', 'No se pudo actualizar el usuario.', 'error');
        }
    });
}

/* ── ELIMINAR USUARIO ── */
function abrirEliminarUsuario(id) {
    document.getElementById('eliminarUserId').value = id;
    document.getElementById('alertEliminarUsuario').innerHTML = '';
    abrirModalAdmin('modalEliminarUsuario');
}

function confirmarEliminarUsuario() {
    const id = document.getElementById('eliminarUserId').value;
    ajax({ accion: 'eliminar', id }, r => {
        if (r.ok) {
            cerrarModalAdmin('modalEliminarUsuario');
            cargarUsuariosAdmin(document.getElementById('adminBuscador').value);
        } else {
            showAdminAlert('alertEliminarUsuario', 'No se pudo eliminar el usuario.', 'error');
        }
    });
}

/* ── HISTORIAL DE USUARIO ── */
function abrirHistorialUsuario(id, nombre) {
    document.getElementById('modalHistTitulo').textContent = 'Movimientos de ' + nombre;
    document.getElementById('modalHistSub').textContent    = '';
    document.getElementById('modalHistorialContenido').innerHTML = '<div style="text-align:center;padding:32px;color:var(--eco-muted)"><span class="spinner-eco" style="border-top-color:var(--eco-primary);border-color:var(--eco-border);border-top-color:var(--eco-primary)"></span></div>';
    abrirModalAdmin('modalHistorialUsuario');

    ajax({ accion: 'adminHistorialConCuenta', id }, r => {
        if (!r.ok) {
            document.getElementById('modalHistorialContenido').innerHTML = '<div style="text-align:center;padding:32px;color:var(--eco-muted)">No se pudieron cargar los datos.</div>';
            return;
        }

        const cuenta   = r.cuenta;
        const historial = r.historial || [];

        document.getElementById('modalHistSub').textContent = cuenta ? 'IBAN: ' + (cuenta.iban || '—') : '';

        let html = '';

        if (cuenta) {
            html += `<div class="usuario-modal-header">
                <div class="usuario-modal-avatar">${iniAvatar(nombre)}</div>
                <div>
                    <div class="usuario-modal-nombre">${htmlE(nombre)}</div>
                    <div class="usuario-modal-email">IBAN: <span style="font-family:'Courier New',monospace;font-size:11px">${htmlE(cuenta.iban || '—')}</span></div>
                </div>
                <div class="usuario-modal-saldo">
                    <div class="usuario-modal-saldo-label">Saldo</div>
                    <div class="usuario-modal-saldo-val">${formatEur(cuenta.saldo)}</div>
                </div>
            </div>`;
        }

        if (!historial.length) {
            html += '<div style="text-align:center;padding:32px;color:var(--eco-muted);font-size:13px">Este usuario no tiene movimientos registrados.</div>';
        } else {
            html += `<div class="hist-modal-scroll">
                <table class="hist-modal-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Concepto</th>
                            <th>Importe</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>`;
            html += historial.map(t => {
                const ent = t.direccion === 'entrada';
                const tipoBadge = t.tipo === 'bizum'
                    ? '<span class="tipo-badge tipo-bizum"><i class="bi bi-lightning-charge-fill"></i> Bizum</span>'
                    : '<span class="tipo-badge tipo-trans"><i class="bi bi-bank"></i> Transferencia</span>';
                const estadoBadge = `<span class="mov-badge ${htmlE(t.estado)}">${htmlE(t.estado)}</span>`;
                const titulo = t.destinatario || t.concepto || '—';
                return `<tr>
                    <td>${tipoBadge}</td>
                    <td><div style="font-weight:500;font-size:13px">${htmlE(titulo)}</div></td>
                    <td style="font-weight:600;${ent ? 'color:var(--eco-primary)' : 'color:#e63946'}">${ent ? '+' : '-'}${formatEur(t.importe)}</td>
                    <td>${estadoBadge}</td>
                    <td style="font-size:12px;white-space:nowrap">${formatFecha(t.fecha)}</td>
                </tr>`;
            }).join('');
            html += '</tbody></table></div>';
        }

        document.getElementById('modalHistorialContenido').innerHTML = html;
    });
}

/* ══════════════════════════════════════════
   SECCIÓN: CUENTAS
══════════════════════════════════════════ */
function cargarCuentasAdmin() {
    ajax({ accion: 'adminCuentas' }, r => {
        if (!r.ok) return;
        todasLasCuentas = r.cuentas || [];
        document.getElementById('adminCuentasCount').textContent = todasLasCuentas.length + ' cuenta' + (todasLasCuentas.length !== 1 ? 's' : '');
        renderTablaCuentas(todasLasCuentas);
    });
}

function filtrarCuentasAdmin(val) {
    const v = val.toLowerCase();
    const filtradas = todasLasCuentas.filter(c =>
        (c.titular || '').toLowerCase().includes(v) ||
        (c.iban || '').toLowerCase().includes(v)
    );
    renderTablaCuentas(filtradas);
}

function renderTablaCuentas(data) {
    const tbody = document.getElementById('tablaAdminCuentasTbody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--eco-muted)">No se encontraron cuentas.</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(c => {
        const saldo = parseFloat(c.saldo || 0);
        const saldoClass = saldo > 0 ? 'saldo-positivo' : 'saldo-cero';
        const estadoBadge = c.activa == 1
            ? '<span class="cuenta-estado cuenta-activa"><i class="bi bi-check-circle-fill"></i> Activa</span>'
            : '<span class="cuenta-estado cuenta-inactiva"><i class="bi bi-slash-circle-fill"></i> Inactiva</span>';
        const av = iniAvatar(c.titular);
        return `<tr>
            <td style="font-size:12px;color:var(--eco-muted);font-weight:600">#${c.id}</td>
            <td>
                <div class="admin-user-cell">
                    <div class="admin-user-avatar">${av}</div>
                    <div>
                        <div class="admin-user-name">${htmlE(c.titular || '—')}</div>
                        <div class="admin-user-id">${htmlE(c.email || '')}</div>
                    </div>
                </div>
            </td>
            <td><span class="iban-mini">${htmlE(c.iban || '—')}</span></td>
            <td><span style="font-size:12px;text-transform:capitalize;color:var(--eco-muted)">${htmlE(c.tipo || 'corriente')}</span></td>
            <td class="${saldoClass}">${formatEur(saldo)}</td>
            <td>${estadoBadge}</td>
            <td>
                <button class="btn-accion info" title="Ver movimientos" onclick="abrirHistorialUsuario(${c.usuario_id}, '${htmlE(c.titular || '')}')">
                    <i class="bi bi-clock-history"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

/* ══════════════════════════════════════════
   SECCIÓN: MOVIMIENTOS
══════════════════════════════════════════ */
function cargarMovimientosAdmin() {
    ajax({ accion: 'adminMovimientos' }, r => {
        if (!r.ok) return;
        todosLosMovimientos = r.movimientos || [];

        const stats = r.stats || {};
        document.getElementById('adminMovTotal').textContent    = stats.total || 0;
        document.getElementById('adminMovMes').textContent      = (stats.este_mes || 0) + ' este mes';
        document.getElementById('adminMovVolBizum').textContent  = formatEur(stats.vol_bizum || 0);
        document.getElementById('adminMovVolTrans').textContent  = formatEur(stats.vol_trans || 0);

        renderTablaMovimientos(todosLosMovimientos);
    });
}

function filtrarMovimientosAdmin() {
    const tipo   = document.getElementById('adminFiltroTipo').value;
    const estado = document.getElementById('adminFiltroEstado').value;
    let data = todosLosMovimientos;
    if (tipo)   data = data.filter(m => m.tipo === tipo);
    if (estado) data = data.filter(m => m.estado === estado);
    renderTablaMovimientos(data);
}

function renderTablaMovimientos(data) {
    const tbody = document.getElementById('tablaAdminMovimientosTbody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--eco-muted)">No se encontraron movimientos.</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(m => {
        const tipoBadge = m.tipo === 'bizum'
            ? '<span class="tipo-badge tipo-bizum"><i class="bi bi-lightning-charge-fill"></i> Bizum</span>'
            : '<span class="tipo-badge tipo-trans"><i class="bi bi-bank"></i> Transferencia</span>';
        const estadoBadge = `<span class="mov-badge ${htmlE(m.estado)}">${htmlE(m.estado)}</span>`;
        const titulo = m.destinatario || m.concepto || '—';
        return `<tr>
            <td>${tipoBadge}</td>
            <td>
                <div style="font-weight:500;font-size:13px">${htmlE(titulo)}</div>
                <div style="font-size:11px;color:var(--eco-muted)">${htmlE(m.concepto || '')}</div>
            </td>
            <td><span class="iban-mini" style="font-size:11px">${htmlE(m.cuenta_origen_iban || '—')}</span></td>
            <td style="font-weight:600;color:var(--eco-text)">${formatEur(m.importe)}</td>
            <td>${estadoBadge}</td>
            <td style="font-size:12px;white-space:nowrap;color:var(--eco-muted)">${formatFecha(m.fecha)}</td>
        </tr>`;
    }).join('');
}

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    cargarResumenAdmin();
});
