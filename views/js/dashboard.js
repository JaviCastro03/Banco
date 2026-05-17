/* ═══════════════════════════════════════════
       NAVIGATION
    ═══════════════════════════════════════════ */
let chartInstance = null;
let historialData = [];
const CONTROLLER = 'index.php';

function showSection(name, el) {
    document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('sec-' + name).classList.add('active');
    if (el) el.classList.add('active');
    else {
        const found = document.querySelector('[data-section="'+name+'"]');
        if (found) found.classList.add('active');
    }
    const titles = {inicio:'Inicio <span>/ Dashboard</span>',bizum:'Bizum <span>/ Enviar</span>',transferencia:'Transferencia <span>/ Nueva</span>',historial:'Historial <span>/ Movimientos</span>',grafica:'Análisis <span>/ Gráfica</span>',tarjeta:'Tarjeta <span>/ Mi tarjeta</span>',perfil:'Perfil <span>/ Mi cuenta</span>'};
    document.getElementById('topbarTitle').innerHTML = titles[name] || name;
    if (name === 'inicio')    loadDashboard();
    if (name === 'historial') loadHistorial();
    if (name === 'grafica')   loadGrafica();
    if (name === 'tarjeta')   loadTarjeta();
    if (name === 'perfil')    loadPerfil();
    document.getElementById('sidebar').classList.remove('open');
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

/* ═══════════════════════════════════════════
   AJAX helper
═══════════════════════════════════════════ */
function ajax(data, cb) {
    data.ajax = 1;
    fetch(CONTROLLER, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: Object.entries(data).map(([k,v])=>encodeURIComponent(k)+'='+encodeURIComponent(v)).join('&')
    }).then(r=>r.json()).then(cb).catch(e=>console.error(e));
}

/* ═══════════════════════════════════════════
   ALERTS
═══════════════════════════════════════════ */
function showAlert(id, msg, type='success') {
    const icons = {success:'check-circle-fill', error:'exclamation-triangle-fill', info:'info-circle-fill'};
    document.getElementById(id).innerHTML = `<div class="eco-alert eco-alert-${type}"><i class="bi bi-${icons[type]}"></i><span>${msg}</span></div>`;
    if (type==='success') setTimeout(()=>{const el=document.getElementById(id);if(el)el.innerHTML='';},4000);
}

/* ═══════════════════════════════════════════
   FORMATO
═══════════════════════════════════════════ */
function formatEur(n) {
    return '€ ' + parseFloat(n||0).toLocaleString('es-ES',{minimumFractionDigits:2,maximumFractionDigits:2});
}
function formatFecha(f) {
    if(!f) return '—';
    const d=new Date(f);
    const hoy=new Date(); const ayer=new Date(hoy); ayer.setDate(ayer.getDate()-1);
    if(d.toDateString()===hoy.toDateString()) return 'Hoy · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
    if(d.toDateString()===ayer.toDateString()) return 'Ayer · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
    return d.toLocaleDateString('es-ES',{day:'2-digit',month:'short'})+' · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
}
function iniAvatar(nombre) {
    return (nombre||'U').trim().charAt(0).toUpperCase();
}

/* ═══════════════════════════════════════════
   DASHBOARD
═══════════════════════════════════════════ */
function loadDashboard() {
    ajax({accion:'getDashboard'}, r=>{
        if(!r.ok) return;
        const d=r.datos, res=r.resumen, mov=r.movimientos;
        const nombre = (d.nombre||'').trim();
        const av = iniAvatar(nombre);
        document.getElementById('statSaldo').textContent    = formatEur(d.saldo);
        document.getElementById('statIban').textContent     = d.iban ? d.iban.replace(/(.{4})/g,'$1 ').trim() : '—';
        document.getElementById('statEnviado').textContent  = formatEur(res.enviado);
        document.getElementById('statRecibido').textContent = formatEur(res.recibido);
        document.getElementById('statNumEnv').textContent   = res.num_enviadas || 0;
        document.getElementById('sidebarName').textContent  = nombre;
        document.getElementById('sidebarAvatar').textContent= av;
        document.getElementById('topAvatar').textContent    = av;
        document.getElementById('welcomeName').textContent  = nombre;
        renderMovList('lastMovList', mov);
    });
}

function renderMovList(id, movs) {
    const ul = document.getElementById(id);
    if(!movs||!movs.length){ul.innerHTML='<li class="mov-item"><div style="color:var(--eco-muted);font-size:13px">Sin movimientos recientes.</div></li>';return;}
    ul.innerHTML = movs.map(m=>{
        const ent = m.direccion==='entrada';
        const icon = m.tipo==='bizum' ? '<i class="bi bi-lightning-charge-fill"></i>' : '<i class="bi bi-bank"></i>';
        const titulo = m.destinatario || m.concepto || (m.tipo==='bizum'?'Bizum':'Transferencia');
        return `<li class="mov-item">
      <div class="mov-icon ${m.direccion}">${icon}</div>
      <div class="mov-info">
        <div class="mov-name">${htmlE(titulo)}</div>
        <div class="mov-date">${formatFecha(m.fecha)}</div>
      </div>
      <div class="mov-amount ${ent?'positivo':'negativo'}">${ent?'+':'-'}${formatEur(m.importe)}</div>
    </li>`;
    }).join('');
}

/* ═══════════════════════════════════════════
   HISTORIAL
═══════════════════════════════════════════ */
function loadHistorial() {
    ajax({accion:'getHistorial'}, r=>{
        if(!r.ok) return;
        historialData = r.historial || [];
        renderHistorial(historialData);
    });
}

function renderHistorial(data) {
    const tbody = document.getElementById('histBody');
    if(!data.length){tbody.innerHTML='<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--eco-muted)">Sin movimientos registrados.</td></tr>';return;}
    tbody.innerHTML = data.map(m=>{
        const ent = m.direccion==='entrada';
        const tipoBadge = m.tipo==='bizum'
            ? '<span class="tipo-badge tipo-bizum"><i class="bi bi-lightning-charge-fill"></i> Bizum</span>'
            : '<span class="tipo-badge tipo-trans"><i class="bi bi-bank"></i> Transferencia</span>';
        const estadoBadge = `<span class="mov-badge ${m.estado}">${m.estado}</span>`;
        const titulo = m.destinatario || m.concepto || '—';
        return `<tr>
      <td>${tipoBadge}</td>
      <td><div style="font-weight:500;font-size:13px">${htmlE(titulo)}</div><div style="font-size:11px;color:var(--eco-muted)">${htmlE(m.concepto||'')}</div></td>
      <td style="font-size:12px;white-space:nowrap">${formatFecha(m.fecha)}</td>
      <td>${estadoBadge}</td>
      <td style="font-weight:600;${ent?'color:var(--eco-primary)':'color:#e63946'}">${ent?'+':'-'}${formatEur(m.importe)}</td>
    </tr>`;
    }).join('');
}

function filtrarHistorial() {
    const tipo = document.getElementById('filtroTipo').value;
    const dir  = document.getElementById('filtroDir').value;
    let data = historialData;
    if(tipo) data = data.filter(m=>m.tipo===tipo);
    if(dir)  data = data.filter(m=>m.direccion===dir);
    renderHistorial(data);
}

/* ═══════════════════════════════════════════
   GRÁFICA
═══════════════════════════════════════════ */
function loadGrafica(filtro = 'meses') {
    ajax({accion:'getGrafica', filtro}, r=>{
        if(!r.ok) return;
        const data = r.datos || [];

        const subtitulos = {
            dias:    'Últimos 30 días',
            semanas: 'Últimas 12 semanas',
            meses:   'Últimos 6 meses',
            años:    'Últimos 5 años'
        };
        const sub = document.getElementById('grafSubtitle');
        if (sub) sub.textContent = subtitulos[filtro] || '';

        const labels = data.map(d => {
            if (filtro === 'dias') {
                const parts = d.periodo.split('-');
                return parts[2] + '/' + parts[1];
            }
            if (filtro === 'semanas') {
                const [anio, sem] = d.periodo.split('-W').map(Number);
                const lunes = new Date(anio, 0, 1 + (sem - 1) * 7);
                const dia = lunes.getDay();
                const diff = dia === 0 ? -6 : (1 - dia);
                lunes.setDate(lunes.getDate() + diff);
                const domingo = new Date(lunes);
                domingo.setDate(lunes.getDate() + 6);
                const fmt = d => d.toLocaleDateString('es-ES', {day:'2-digit', month:'2-digit'});
                return `${fmt(lunes)} - ${fmt(domingo)}`;
            }
            if (filtro === 'años')    return d.periodo;
            const p = d.periodo.split('-');
            return ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][parseInt(p[1])-1] + ' ' + p[0].slice(2);
        });

        const gastado  = data.map(d => parseFloat(d.gastado)  || 0);
        const recibido = data.map(d => parseFloat(d.recibido) || 0);

        document.getElementById('grafEnviado').textContent  = formatEur(gastado.reduce((a,b)=>a+b,0));
        document.getElementById('grafRecibido').textContent = formatEur(recibido.reduce((a,b)=>a+b,0));

        if(chartInstance) chartInstance.destroy();
        const ctx = document.getElementById('myChart').getContext('2d');
        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label:'Recibido', data: recibido, backgroundColor:'rgba(45,106,79,.75)', borderRadius:8, borderSkipped:false },
                    { label:'Gastado',  data: gastado,  backgroundColor:'rgba(230,57,70,.7)',  borderRadius:8, borderSkipped:false }
                ]
            },
            options: {
                responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label: ctx=>' '+formatEur(ctx.raw) } } },
                scales:{
                    x:{ grid:{ display:false }, ticks:{ font:{ family:"'DM Sans',sans-serif", size:12 } } },
                    y:{ grid:{ color:'rgba(0,0,0,.05)' }, ticks:{ callback:v=>formatEur(v), font:{ size:11 } } }
                }
            }
        });
    });
}

function cambiarFiltroGrafica(filtro, el) {
    document.querySelectorAll('.filtro-btn').forEach(b => {
        b.classList.remove('eco-btn-primary');
        b.classList.add('eco-btn-outline');
    });
    el.classList.remove('eco-btn-outline');
    el.classList.add('eco-btn-primary');
    loadGrafica(filtro);
}

/* ═══════════════════════════════════════════
   TARJETA
═══════════════════════════════════════════ */
function loadTarjeta() {
    ajax({accion:'getTarjeta'}, r=>{
        if(!r.ok) return;
        if(r.tarjeta) renderTarjeta(r.tarjeta);
    });
}

function renderTarjeta(t) {
    const num    = t.numero_tarjeta;
    const masked = '**** **** **** ' + num.slice(-4);
    const exp    = t.fecha_caducidad ? t.fecha_caducidad.slice(0,7).replace('-','/').split('/').reverse().join('/') : '??/??';
    document.getElementById('cardDisplay').innerHTML = `
    <div class="bank-card">
      <div class="card-type-badge">${t.tipo||'débito'}</div>
      <div class="card-chip"></div>
      <div class="card-number">${masked}</div>
      <div class="card-row">
        <div>
          <div class="card-label-sm">Titular</div>
          <div class="card-val">${htmlE(t.titular||'')}</div>
        </div>
        <div>
          <div class="card-label-sm">Válida hasta</div>
          <div class="card-val">${exp}</div>
        </div>
        <div class="card-brand">VISA</div>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <div class="eco-card p-3 flex-fill text-center">
        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">CVV</div>
        <div style="font-size:20px;font-weight:700;letter-spacing:4px">•••</div>
      </div>
      <div class="eco-card p-3 flex-fill text-center">
        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Estado</div>
        <div style="font-size:13px;font-weight:600;color:var(--eco-primary)"><i class="bi bi-check-circle-fill me-1"></i> Activa</div>
      </div>
    </div>`;
}

function crearTarjeta() {
    showAlert('alertTarjeta','Creando tarjeta...','info');
    ajax({accion:'crearTarjeta'}, r=>{
        showAlert('alertTarjeta', r.mensaje, r.ok?'success':'error');
        if(r.ok && r.tarjeta) {
            renderTarjeta(r.tarjeta);
        }
    });
}

/* ═══════════════════════════════════════════
   PERFIL
═══════════════════════════════════════════ */
function loadPerfil() {
    ajax({accion:'getDashboard'}, r=>{
        if(!r.ok) return;
        const d=r.datos;
        const nombre=(d.nombre||'').trim(), ape=(d.apellidos||'').trim();
        document.getElementById('perfilAvatar').textContent     = iniAvatar(nombre);
        document.getElementById('perfilNombreH').textContent    = nombre+' '+ape;
        document.getElementById('perfilEmailH').textContent     = d.email||'';
        document.getElementById('perfilIBAN').textContent       = d.iban||'—';
        document.getElementById('perfilTipoCuenta').textContent = d.tipo_cuenta||'corriente';
        document.getElementById('perfilDNI').textContent        = d.dni||'—';
        document.getElementById('perfilDesde').textContent      = d.created_at ? new Date(d.created_at).toLocaleDateString('es-ES',{year:'numeric',month:'long'}) : '—';
        document.getElementById('pNombre').value    = nombre;
        document.getElementById('pApellidos').value = ape;
        document.getElementById('pEmail').value     = d.email||'';
        document.getElementById('pTelefono').value  = (d.telefono||'').trim();
        document.getElementById('pProvincia').value = d.provincia||'';
        document.getElementById('pMunicipio').value = d.municipio||'';
        document.getElementById('pVia').value       = d.via||'';
        document.getElementById('pNumPiso').value   = d.numero_piso||'';
    });
}

function guardarPerfil() {
    ajax({
        accion:'actualizarPerfil',
        nombre:      document.getElementById('pNombre').value,
        apellidos:   document.getElementById('pApellidos').value,
        email:       document.getElementById('pEmail').value,
        telefono:    document.getElementById('pTelefono').value,
        provincia:   document.getElementById('pProvincia').value,
        municipio:   document.getElementById('pMunicipio').value,
        via:         document.getElementById('pVia').value,
        numero_piso: document.getElementById('pNumPiso').value
    }, r => showAlert('alertPerfil', r.mensaje, r.ok?'success':'error'));
}

function cambiarPass() {
    const act=document.getElementById('passActual').value;
    const nva=document.getElementById('passNueva').value;
    const rep=document.getElementById('passRepeat').value;
    if(nva!==rep){showAlert('alertPass','Las contraseñas nuevas no coinciden.','error');return;}
    if(nva.length<6){showAlert('alertPass','La nueva contraseña debe tener al menos 6 caracteres.','error');return;}
    ajax({accion:'cambiarPassword',password_actual:act,nueva_password:nva}, r=>{
        showAlert('alertPass',r.mensaje,r.ok?'success':'error');
        if(r.ok){document.getElementById('passActual').value='';document.getElementById('passNueva').value='';document.getElementById('passRepeat').value='';}
    });
}

function copiarIban() {
    const iban = document.getElementById('perfilIBAN').textContent;
    navigator.clipboard.writeText(iban).then(()=>showAlert('alertPerfil','IBAN copiado al portapapeles.','success'));
}


function solicitarCodigoBizum() {
    const tel  = document.getElementById('bizumTel').value.trim();
    const imp  = document.getElementById('bizumImporte').value;
    const conc = document.getElementById('bizumConcepto').value.trim();

    if (!tel || !imp || parseFloat(imp) <= 0) {
        showAlert('alertBizum', 'Rellena el teléfono y el importe correctamente.', 'error');
        return;
    }

    const btn = document.getElementById('btnEnviarBizum');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-eco"></span> Enviando...';

    ajax({
        accion: 'solicitarCodigoBizum',
        telefono: tel,
        importe: imp,
        concepto: conc || 'Bizum'
    }, r => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-charge-fill"></i> Enviar Bizum';

        if (r.ok) {
            showAlert('alertBizum', r.mensaje, 'success');
            abrirModal('modalBizum');
            limpiarCodeInputs('codeInputsBizum');
        } else {
            showAlert('alertBizum', r.mensaje, 'error');
        }
    });
}

function confirmarBizum() {
    const codigo = getCodeValue('codeInputsBizum');

    if (codigo.length !== 5) {
        showAlert('alertCodBizum', 'Introduce los 5 dígitos del código.', 'error');
        return;
    }

    const btn = document.getElementById('btnConfBizum');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-eco"></span>';

    ajax({ accion: 'confirmarBizum', codigo }, r => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2-circle"></i> Confirmar';

        if (r.ok) {
            cerrarModal('modalBizum');
            showAlert('alertBizum', r.mensaje, 'success');

            document.getElementById('bizumTel').value = '';
            document.getElementById('bizumImporte').value = '';
            document.getElementById('bizumConcepto').value = '';

            loadDashboard();
            cargarUltimosBizum();

        } else {
            showAlert('alertCodBizum', r.mensaje, 'error');
        }
    });
}

function cargarUltimosBizum() {
    ajax({ accion: 'ultimosBizum' }, r => {
        const cont = document.getElementById('bizumRecientes');

        if (!cont) return;

        if (!r.ok || !r.datos || !r.datos.length) {
            cont.innerHTML = '<span class="text-muted">Sin contactos recientes</span>';
            return;
        }

        cont.innerHTML = r.datos.map(x => `
            <button
                class="eco-btn eco-btn-outline"
                onclick="seleccionarBizum('${x.telefono_destino}')">
                ${htmlE(x.nombre_destino)}
            </button>
        `).join('');
    });
}

function seleccionarBizum(telefono) {
    document.getElementById('bizumTel').value = telefono;
}
/* ═══════════════════════════════════════════
   TRANSFERENCIA
═══════════════════════════════════════════ */
function solicitarCodigoTrans() {
    const iban  = document.getElementById('transIBAN').value.trim().replace(/\s/g,'');
    const tit   = document.getElementById('transTitular').value.trim();
    const imp   = document.getElementById('transImporte').value;
    const conc  = document.getElementById('transConcepto').value.trim();
    if(!iban||!tit||!imp||parseFloat(imp)<=0){showAlert('alertTrans','Rellena todos los campos obligatorios.','error');return;}
    const btn=document.getElementById('btnEnviarTrans');
    btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span> Verificando...';
    ajax({accion:'solicitarCodigoTransferencia',iban_destino:iban,titular_destino:tit,importe:imp,concepto:conc||'Transferencia'}, r=>{
        btn.disabled=false; btn.innerHTML='<i class="bi bi-arrow-right-circle-fill"></i> Realizar transferencia';
        if(r.ok){showAlert('alertTrans',r.mensaje,'success'); abrirModal('modalTrans'); limpiarCodeInputs('codeInputsTrans');}
        else showAlert('alertTrans',r.mensaje,'error');
    });
}

function confirmarTrans() {
    const codigo = getCodeValue('codeInputsTrans');
    if(codigo.length!==5){showAlert('alertCodTrans','Introduce los 5 dígitos del código.','error');return;}
    const btn=document.getElementById('btnConfTrans');
    btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span>';
    ajax({accion:'confirmarTransferencia',codigo}, r=>{
        btn.disabled=false; btn.innerHTML='<i class="bi bi-check2-circle"></i> Confirmar';
        if(r.ok){
            cerrarModal('modalTrans');
            showAlert('alertTrans',r.mensaje,'success');
            document.getElementById('transIBAN').value='';
            document.getElementById('transTitular').value='';
            document.getElementById('transImporte').value='';
            document.getElementById('transConcepto').value='';
            loadDashboard();
        } else showAlert('alertCodTrans',r.mensaje,'error');
    });
}

/* ═══════════════════════════════════════════
   MODALES & CODE INPUTS
═══════════════════════════════════════════ */
function abrirModal(id) { document.getElementById(id).classList.add('open'); }
function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }

function getCodeValue(containerId) {
    return Array.from(document.querySelectorAll('#'+containerId+' .code-input')).map(i=>i.value).join('');
}
function limpiarCodeInputs(containerId) {
    document.querySelectorAll('#'+containerId+' .code-input').forEach(i=>i.value='');
}

// Auto-advance code inputs
document.querySelectorAll('.code-inputs').forEach(container=>{
    const inputs = container.querySelectorAll('.code-input');
    inputs.forEach((inp,i)=>{
        inp.addEventListener('input',e=>{
            inp.value = inp.value.replace(/[^0-9]/g,'').slice(-1);
            if(inp.value && i<inputs.length-1) inputs[i+1].focus();
        });
        inp.addEventListener('keydown',e=>{
            if(e.key==='Backspace'&&!inp.value&&i>0) inputs[i-1].focus();
        });
        inp.addEventListener('paste',e=>{
            e.preventDefault();
            const paste=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
            inputs.forEach((inp2,j)=>{ inp2.value=paste[j]||''; });
            inputs[Math.min(paste.length,inputs.length)-1].focus();
        });
    });
});

// Cerrar modal clickando fuera
document.querySelectorAll('.modal-overlay').forEach(m=>{
    m.addEventListener('click',e=>{ if(e.target===m) m.classList.remove('open'); });
});

/* ═══════════════════════════════════════════
   NOTIFICACIONES
═══════════════════════════════════════════ */
function toggleNotif() {
    const p=document.getElementById('notifPanel');
    p.classList.toggle('open');
    if(p.classList.contains('open')) loadNotifs();
}

function loadNotifs() {
    ajax({accion:'getNotificaciones'}, r=>{
        if(!r.ok) return;
        const badge=document.getElementById('notifBadge');
        const n=r.no_leidas||0;
        badge.textContent=n; badge.style.display=n>0?'flex':'none';
        const list=document.getElementById('notifList');
        if(!r.notificaciones||!r.notificaciones.length){list.innerHTML='<div class="notif-empty"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>Sin notificaciones</div>';return;}
        list.innerHTML=r.notificaciones.map(n=>`
      <div class="notif-item ${n.leida?'':'no-leida'}">
        <div class="notif-dot ${n.leida?'leida':''}"></div>
        <div>
          <div class="notif-text">${htmlE(n.contenido)}</div>
          <div class="notif-time">${formatFecha(n.created_at)}</div>
        </div>
      </div>`).join('');
    });
}

function marcarLeidas() {
    ajax({accion:'marcarLeidas'}, ()=>loadNotifs());
}

// Cerrar notif panel clickando fuera
document.addEventListener('click', e=>{
    const panel=document.getElementById('notifPanel');
    const btn=document.getElementById('btnNotif');
    if(!panel.contains(e.target)&&!btn.contains(e.target)) panel.classList.remove('open');
});

/* ═══════════════════════════════════════════
   UTILS
═══════════════════════════════════════════ */
function htmlE(str) {
    if(!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══════════════════════════════════════════
   INIT
═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
    cargarUltimosBizum();

    ajax({ accion:'getNotificaciones' }, r => {
        if (r.ok) {
            const n = r.no_leidas || 0;
            const b = document.getElementById('notifBadge');

            b.textContent = n;
            b.style.display = n > 0 ? 'flex' : 'none';
        }
    });
});