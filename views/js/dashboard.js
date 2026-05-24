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
    const titles = {inicio:'Inicio <span>/ Dashboard</span>',bizum:'Bizum <span>/ Enviar</span>',transferencia:'Transferencia <span>/ Nueva</span>',historial:'Historial <span>/ Movimientos</span>',grafica:'Análisis <span>/ Gráfica</span>',tarjeta:'Tarjeta <span>/ Mi tarjeta</span>',perfil:'Perfil <span>/ Mi cuenta</span>',simulaciones:'Mensualidades <span>/ Ingresos y Gastos</span>'};
    document.getElementById('topbarTitle').innerHTML = titles[name] || name;
    if (name === 'inicio')        loadDashboard();
    if (name === 'historial')     loadHistorial();
    if (name === 'grafica')       { loadGrafica(); loadDesglose(); }
    if (name === 'tarjeta')       loadTarjeta();
    if (name === 'perfil')        loadPerfil();
    if (name === 'mensualidades')  loadSimulaciones();
    if (name === 'bizum')         loadBizumRecientes();
    if (name === 'transferencia') loadTransRecientes();
    document.getElementById('sidebar').classList.remove('open'); const _ov=document.getElementById('sidebarOverlay'); if(_ov) _ov.style.display='none';
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
    // Overlay para cerrar en móvil
    let overlay = document.getElementById('sidebarOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sidebarOverlay';
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99;display:none';
        overlay.onclick = () => { sidebar.classList.remove('open'); overlay.style.display='none'; };
        document.body.appendChild(overlay);
    }
    overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
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
    setTimeout(()=>{const el=document.getElementById(id);if(el)el.innerHTML='';},10000);
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

// renderHistorial con paginación — definida al final del archivo

function filtrarHistorial() {
    const tipo = document.getElementById('filtroTipo').value;
    const dir  = document.getElementById('filtroDir').value;
    let data = historialData;
    if(tipo) data = data.filter(m=>m.tipo===tipo);
    if(dir)  data = data.filter(m=>m.direccion===dir);
    histPagina = 1;
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
   DESGLOSE POR TIPO (gráfico sectores)
═══════════════════════════════════════════ */
let chartSectores = null;
let desgloseFiltro = 'total';

function loadDesglose(filtro) {
    if (filtro) desgloseFiltro = filtro;

    // Actualizar botones
    document.querySelectorAll('.desglose-btn').forEach(b => {
        b.classList.toggle('pag-active', b.dataset.filtro === desgloseFiltro);
    });

    ajax({accion: 'getDesglose', filtro: desgloseFiltro}, r => {
        if (!r.ok) return;
        const data = r.desglose || [];

        const categorias = {
            'bizum_salida':          { label: 'Bizum enviado',           color: '#e63946' },
            'bizum_entrada':         { label: 'Bizum recibido',          color: '#2d6a4f' },
            'transferencia_salida':  { label: 'Transferencia enviada',   color: '#f4a261' },
            'transferencia_entrada': { label: 'Transferencia recibida',  color: '#52b788' },
        };

        const totales = {};
        data.forEach(d => {
            totales[`${d.tipo}_${d.direccion}`] = { total: parseInt(d.total), volumen: parseFloat(d.volumen) };
        });

        const labels = [], valores = [], colores = [], filas = [];
        Object.entries(categorias).forEach(([key, cat]) => {
            if (totales[key]) {
                labels.push(cat.label);
                valores.push(totales[key].volumen);
                colores.push(cat.color);
                filas.push({ ...cat, total: totales[key].total, volumen: totales[key].volumen });
            }
        });

        const sinDatos = document.getElementById('desgloseVacio');
        if (!valores.length) {
            if (sinDatos) sinDatos.style.display = 'block';
            document.getElementById('chartSectores').style.display = 'none';
            document.getElementById('desgloseTabla').innerHTML = '<div style="color:var(--eco-muted);font-size:13px;padding:20px 0">Sin operaciones en este periodo.</div>';
            return;
        }
        if (sinDatos) sinDatos.style.display = 'none';
        document.getElementById('chartSectores').style.display = 'block';

        if (chartSectores) chartSectores.destroy();
        const ctx = document.getElementById('chartSectores').getContext('2d');
        chartSectores = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: valores, backgroundColor: colores, borderWidth: 2, borderColor: '#fff', hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${formatEur(ctx.raw)}` } }
                }
            }
        });

        const totalVol = filas.reduce((a, f) => a + f.volumen, 0);
        document.getElementById('desgloseTabla').innerHTML = filas.map(f => `
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--eco-border)">
                <div style="width:12px;height:12px;border-radius:50%;background:${f.color};flex-shrink:0"></div>
                <div style="flex:1;font-size:13px;font-weight:500">${f.label}</div>
                <div style="text-align:right">
                    <div style="font-size:13px;font-weight:700">${formatEur(f.volumen)}</div>
                    <div style="font-size:11px;color:var(--eco-muted)">${f.total} op. · ${Math.round(f.volumen/totalVol*100)}%</div>
                </div>
            </div>`).join('');
    });
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

function verDatosTarjeta(t) {
    const exp = t.fecha_caducidad
        ? t.fecha_caducidad.slice(0,7).replace('-','/').split('/').reverse().join('/')
        : '??/??';

    document.getElementById('modalTarjetaBody').innerHTML = `
        <div class="mb-3">
            <label class="fw-bold">Titular</label>
            <div class="eco-card p-2 mt-1">${htmlE(t.titular || '')}</div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Número de tarjeta</label>
            <div class="eco-card p-2 mt-1">${htmlE(t.numero_tarjeta || '')}</div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">Fecha de caducidad</label>
            <div class="eco-card p-2 mt-1">${exp}</div>
        </div>

        <div class="mb-3">
            <label class="fw-bold">CVV</label>
            <div class="eco-card p-2 mt-1">${htmlE(t.cvv || '')}</div>
        </div>
    `;

    abrirModal('modalTarjetaDatos');
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

/* ═══════════════════════════════════════════
   BIZUM
═══════════════════════════════════════════ */
function solicitarCodigoBizum() {
    const tel  = document.getElementById('bizumTel').value.trim();
    const imp  = document.getElementById('bizumImporte').value;
    const conc = document.getElementById('bizumConcepto').value.trim();
    if(!tel||!imp||parseFloat(imp)<=0){showAlert('alertBizum','Rellena el teléfono y el importe correctamente.','error');return;}
    const btn=document.getElementById('btnEnviarBizum');
    btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span> Enviando...';
    ajax({accion:'solicitarCodigoBizum',telefono:tel,importe:imp,concepto:conc||'Bizum'}, r=>{
        btn.disabled=false; btn.innerHTML='<i class="bi bi-lightning-charge-fill"></i> Enviar Bizum';
        if(r.ok){showAlert('alertBizum',r.mensaje,'success'); abrirModal('modalBizum'); limpiarCodeInputs('codeInputsBizum');}
        else showAlert('alertBizum',r.mensaje,'error');
    });
}

function confirmarBizum() {
    const codigo = getCodeValue('codeInputsBizum');
    if(codigo.length!==5){showAlert('alertCodBizum','Introduce los 5 dígitos del código.','error');return;}
    const btn=document.getElementById('btnConfBizum');
    btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span>';
    ajax({accion:'confirmarBizum',codigo}, r=>{
        btn.disabled=false; btn.innerHTML='<i class="bi bi-check2-circle"></i> Confirmar';
        if(r.ok){
            cerrarModal('modalBizum');
            document.getElementById('modalExitoTitulo').textContent = '¡Bizum enviado!';
            document.getElementById('modalExitoMsg').textContent    = r.mensaje;
            document.getElementById('modalExitoPdfBtn').href        = r.transaccion_id ? `controllers/generarPDF.php?id=${r.transaccion_id}` : '#';
            abrirModal('modalExito');
            document.getElementById('bizumTel').value='';
            document.getElementById('bizumImporte').value='';
            document.getElementById('bizumConcepto').value='';
            loadDashboard();
            loadBizumRecientes();
        } else showAlert('alertCodBizum',r.mensaje,'error');
    });
}

/* ═══════════════════════════════════════════
   TRANSFERENCIA
═══════════════════════════════════════════ */
function validarIBAN(iban) {
    iban = iban.replace(/\s/g, '').toUpperCase();
    if (!/^ES\d{22}$/.test(iban)) return false;
    const reord = iban.slice(4) + iban.slice(0, 4);
    const num = reord.split('').map(c => isNaN(c) ? c.charCodeAt(0) - 55 : c).join('');
    let resto = 0;
    for (const d of num) resto = (resto * 10 + parseInt(d)) % 97;
    return resto === 1;
}

function solicitarCodigoTrans() {
    const iban  = document.getElementById('transIBAN').value.trim().replace(/\s/g,'');
    const tit   = document.getElementById('transTitular').value.trim();
    const imp   = document.getElementById('transImporte').value;
    const conc  = document.getElementById('transConcepto').value.trim();
    if(!iban||!tit||!imp||parseFloat(imp)<=0){showAlert('alertTrans','Rellena todos los campos obligatorios.','error');return;}
    if(!validarIBAN(iban)){showAlert('alertTrans','El IBAN introducido no es válido.','error');return;}
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
            document.getElementById('modalExitoTitulo').textContent = '¡Transferencia enviada!';
            document.getElementById('modalExitoMsg').textContent    = r.mensaje;
            document.getElementById('modalExitoPdfBtn').href        = r.transaccion_id ? `controllers/generarPDF.php?id=${r.transaccion_id}` : '#';
            abrirModal('modalExito');
            document.getElementById('transIBAN').value='';
            document.getElementById('transTitular').value='';
            document.getElementById('transImporte').value='';
            document.getElementById('transConcepto').value='';
            loadDashboard();
            loadTransRecientes();
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
document.addEventListener('DOMContentLoaded', ()=>{
    loadDashboard();
    ajax({accion:'getNotificaciones'}, r=>{
        if(r.ok){ const n=r.no_leidas||0; const b=document.getElementById('notifBadge'); b.textContent=n; b.style.display=n>0?'flex':'none'; }
    });
});
/* ═══════════════════════════════════════════
   SIMULACIONES
═══════════════════════════════════════════ */

function loadSimulaciones() {
    ajax({accion:'getSimulaciones'}, r=>{
        if(!r.ok) return;
        const gastos   = (r.simulaciones||[]).filter(s=>s.tipo==='gasto');
        const ingresos = (r.simulaciones||[]).filter(s=>s.tipo==='ingreso');
        renderSimLista('listaGastos', gastos, 'gasto');
        renderSimLista('listaIngresos', ingresos, 'ingreso');
    });
}

function renderSimLista(containerId, lista, tipo) {
    const el = document.getElementById(containerId);
    if(!el) return;
    if(!lista||!lista.length){ el.innerHTML=`<p style="font-size:13px;color:var(--eco-muted);text-align:center;padding:12px">Sin ${tipo==='gasto'?'gastos':'ingresos'} configurados</p>`; return; }
    el.innerHTML = lista.map(s=>{
        const imp = parseFloat(s.importe).toFixed(2).replace('.',',');
        const dias = s.intervalo_dias===1?'cada día':`cada ${s.intervalo_dias} días`;
        const prox = s.proxima_ejecucion ? new Date(s.proxima_ejecucion).toLocaleDateString('es-ES') : '-';
        const activeIcon = s.activa=='1' ? '<i class="bi bi-pause-fill"></i>' : '<i class="bi bi-play-fill"></i>';
        const color = tipo==='gasto'?'#e63946':'#2d6a4f';
        return `<div class="sim-item ${s.activa=='1'?'':'inactiva'}" id="sim-${s.id}">
            <div class="sim-item-info">
                <div class="sim-item-nombre">${htmlE(s.nombre)}</div>
                <div class="sim-item-detalle">${dias} · Próx: ${prox}</div>
            </div>
            <span class="sim-item-importe ${tipo}" style="color:${color}">${tipo==='gasto'?'-':'+'} €${imp}</span>
            <div class="sim-actions">
                <button class="sim-btn" title="${s.activa=='1'?'Pausar':'Reanudar'}" onclick="toggleSim(${s.id})">${activeIcon}</button>
                <button class="sim-btn danger" title="Eliminar" onclick="eliminarSim(${s.id})"><i class="bi bi-trash3"></i></button>
            </div>
        </div>`;
    }).join('');
}

function quickGasto(nombre, importe, intervalo) {
    ajax({accion:'crearSimulacion', nombre, tipo:'gasto', importe, intervalo_dias:intervalo}, r=>{
        if(r.ok) { loadSimulaciones(); showSimAlert('Simulación "'+nombre+'" añadida.','success'); }
        else showSimAlert(r.mensaje||'Error','error');
    });
}

function crearNomina() {
    const imp = parseFloat(document.getElementById('nominaImporte').value)||0;
    if(imp<=0){ showSimAlert('Introduce un importe válido para la nómina.','error'); return; }
    ajax({accion:'crearSimulacion', nombre:'Nómina', tipo:'ingreso', importe:imp, intervalo_dias:2}, r=>{
        if(r.ok){ document.getElementById('nominaImporte').value=''; loadSimulaciones(); showSimAlert('Nómina configurada. Se ingresará cada 2 días.','success'); }
        else showSimAlert(r.mensaje||'Error','error');
    });
}

function crearSimulacion(tipo) {
    const nombre  = tipo==='gasto' ? document.getElementById('gastoNombre').value.trim()  : document.getElementById('ingresoNombre').value.trim();
    const importe = tipo==='gasto' ? parseFloat(document.getElementById('gastoImporte').value)||0 : parseFloat(document.getElementById('ingresoImporte').value)||0;
    const intervalo = tipo==='gasto' ? parseInt(document.getElementById('gastoIntervalo').value)||1 : parseInt(document.getElementById('ingresoIntervalo').value)||1;
    if(!nombre||importe<=0){ showSimAlert('Rellena nombre e importe.','error'); return; }
    ajax({accion:'crearSimulacion', nombre, tipo, importe, intervalo_dias:intervalo}, r=>{
        if(r.ok){
            if(tipo==='gasto'){document.getElementById('gastoNombre').value='';document.getElementById('gastoImporte').value='';}
            else{document.getElementById('ingresoNombre').value='';document.getElementById('ingresoImporte').value='';}
            loadSimulaciones(); showSimAlert('Simulación añadida correctamente.','success');
        } else showSimAlert(r.mensaje||'Error','error');
    });
}

function eliminarSim(id) {
    ajax({accion:'eliminarSimulacion', id}, r=>{ if(r.ok) loadSimulaciones(); });
}

function toggleSim(id) {
    ajax({accion:'toggleSimulacion', id}, r=>{ if(r.ok) loadSimulaciones(); });
}

function procesarSimulaciones() {
    const btn = document.querySelector('[onclick="procesarSimulaciones()"]');
    if(btn){ btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span> Procesando...'; }
    ajax({accion:'procesarSimulaciones'}, r=>{
        if(btn){ btn.disabled=false; btn.innerHTML='<i class="bi bi-play-circle-fill"></i> Procesar simulaciones pendientes'; }
        if(r.ok){
            const msg = r.procesadas>0
                ? `${r.procesadas} simulación(es) procesada(s). Nuevo saldo: €${parseFloat(r.nuevo_saldo).toFixed(2).replace('.',',')}`
                : 'No hay simulaciones pendientes de ejecutar.';
            showSimAlert(msg, r.procesadas>0?'success':'info');
            loadSimulaciones(); loadDashboard();
        } else showSimAlert(r.mensaje||'Error al procesar.','error');
    });
}

function showSimAlert(msg, type) {
    const el = document.getElementById('alertSim');
    if(!el) return;
    const colors={success:'#d8f3dc',error:'#fde8ea',info:'#e8f4fd'};
    const textColors={success:'#1a3a2a',error:'#7f1d1d',info:'#1a3a7f'};
    el.innerHTML=`<div style="padding:12px 16px;border-radius:10px;font-size:13px;background:${colors[type]||colors.info};color:${textColors[type]||textColors.info}">${htmlE(msg)}</div>`;
    setTimeout(()=>{ if(el) el.innerHTML=''; }, 10000);
}

// Hook into section loading to load simulaciones when opened
const _origShowSection = typeof showSection==='function' ? showSection : null;
document.addEventListener('DOMContentLoaded', ()=>{
    // Auto-load simulaciones when section is visible
    document.querySelectorAll('[onclick*="simulaciones"]').forEach(btn=>{
        btn.addEventListener('click', ()=>{ setTimeout(loadSimulaciones, 200); });
    });
});

/* ═══════════════════════════════════════════
   CONTACTOS RECIENTES — BIZUM
═══════════════════════════════════════════ */
function loadBizumRecientes() {
    ajax({accion: 'ultimosBizum'}, r => {
        const el = document.getElementById('bizumRecientes');
        if (!el) return;
        if (!r.ok || !r.datos || !r.datos.length) {
            el.innerHTML = '<span style="font-size:12px;color:var(--eco-muted)">Sin contactos recientes.</span>';
            return;
        }
        el.innerHTML = r.datos.map(c => `
            <button onclick="rellenarBizum('${htmlE(c.telefono_destino)}')"
                style="display:flex;align-items:center;gap:8px;padding:8px 12px;
                       border-radius:20px;border:1.5px solid var(--eco-border);
                       background:var(--eco-white);cursor:pointer;font-size:12px;
                       font-family:inherit;color:var(--eco-text);transition:all .2s"
                onmouseover="this.style.borderColor='var(--eco-primary)';this.style.background='var(--eco-pale)'"
                onmouseout="this.style.borderColor='var(--eco-border)';this.style.background='var(--eco-white)'">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--eco-pale);
                            display:flex;align-items:center;justify-content:center;
                            color:var(--eco-primary);font-weight:700;font-size:12px;flex-shrink:0">
                    ${htmlE((c.nombre_destino||'?').trim().charAt(0).toUpperCase())}
                </div>
                <div style="text-align:left">
                    <div style="font-weight:600">${htmlE(c.nombre_destino||c.telefono_destino)}</div>
                    <div style="color:var(--eco-muted);font-size:11px">${htmlE(c.telefono_destino)}</div>
                </div>
            </button>`).join('');
    });
}

function rellenarBizum(telefono) {
    document.getElementById('bizumTel').value = telefono;
    document.getElementById('bizumImporte').focus();
}

/* ═══════════════════════════════════════════
   CONTACTOS RECIENTES — TRANSFERENCIA
═══════════════════════════════════════════ */
function loadTransRecientes() {
    ajax({accion: 'ultimasTransferencias'}, r => {
        const el = document.getElementById('transRecientes');
        if (!el) return;
        if (!r.ok || !r.datos || !r.datos.length) {
            el.innerHTML = '<span style="font-size:12px;color:var(--eco-muted)">Sin transferencias recientes.</span>';
            return;
        }
        el.innerHTML = r.datos.map(c => `
            <button onclick="rellenarTrans('${htmlE(c.iban_destino)}','${htmlE(c.titular_destino)}')"
                style="display:flex;align-items:center;gap:8px;padding:8px 12px;
                       border-radius:20px;border:1.5px solid var(--eco-border);
                       background:var(--eco-white);cursor:pointer;font-size:12px;
                       font-family:inherit;color:var(--eco-text);transition:all .2s;width:100%"
                onmouseover="this.style.borderColor='var(--eco-primary)';this.style.background='var(--eco-pale)'"
                onmouseout="this.style.borderColor='var(--eco-border)';this.style.background='var(--eco-white)'">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--eco-pale);
                            display:flex;align-items:center;justify-content:center;
                            color:var(--eco-primary);font-weight:700;font-size:12px;flex-shrink:0">
                    ${htmlE((c.titular_destino||'?').trim().charAt(0).toUpperCase())}
                </div>
                <div style="text-align:left;overflow:hidden">
                    <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${htmlE(c.titular_destino)}</div>
                    <div style="color:var(--eco-muted);font-size:11px;font-family:monospace">${htmlE(c.iban_destino.replace(/(.{4})/g,'$1 ').trim())}</div>
                </div>
            </button>`).join('');
    });
}

function rellenarTrans(iban, titular) {
    document.getElementById('transIBAN').value    = iban;
    document.getElementById('transTitular').value = titular;
    document.getElementById('transImporte').focus();
}

/* ═══════════════════════════════════════════
   POLLING — actualización automática
═══════════════════════════════════════════ */
let lastSaldo      = null;
let pollingInterval = null;

function iniciarPolling() {
    if (pollingInterval) return;
    ajax({accion: 'getSaldoActual'}, r => {
        if (r.ok) lastSaldo = parseFloat(r.saldo);
    });
    pollingInterval = setInterval(() => {
        ajax({accion: 'getSaldoActual'}, r => {
            if (!r.ok) return;
            const nuevoSaldo = parseFloat(r.saldo);
            if (lastSaldo !== null && nuevoSaldo !== lastSaldo) {
                const diferencia = nuevoSaldo - lastSaldo;
                loadDashboard();
                loadHistorial();
                loadNotifs();
                const secGrafica = document.getElementById('sec-grafica');
                if (secGrafica && secGrafica.classList.contains('active')) loadGrafica();
                const secBizum = document.getElementById('sec-bizum');
                if (secBizum && secBizum.classList.contains('active')) loadBizumRecientes();
                const secTrans = document.getElementById('sec-transferencia');
                if (secTrans && secTrans.classList.contains('active')) loadTransRecientes();
                mostrarToast(diferencia);
                lastSaldo = nuevoSaldo;
            }
            lastSaldo = nuevoSaldo;
        });
    }, 10000);
}

function mostrarToast(diferencia) {
    const esIngreso = diferencia > 0;
    const color     = esIngreso ? 'var(--eco-primary)' : '#c0392b';
    const icono     = esIngreso ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
    const texto     = esIngreso
        ? `Has recibido +${Math.abs(diferencia).toFixed(2).replace('.',',')} €`
        : `Pago realizado -${Math.abs(diferencia).toFixed(2).replace('.',',')} €`;

    const notif = document.createElement('div');
    notif.style.cssText = `
        position:fixed;bottom:24px;right:24px;z-index:9999;
        background:${color};color:#fff;
        padding:14px 20px;border-radius:12px;
        font-size:13px;font-weight:600;
        display:flex;align-items:center;gap:10px;
        box-shadow:0 8px 24px rgba(0,0,0,.2);
        animation:slideInNotif .3s ease;
    `;
    notif.innerHTML = `<i class="bi ${icono}"></i> ${texto}`;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 10000);
}

document.addEventListener('DOMContentLoaded', iniciarPolling);
/* ═══════════════════════════════════════════
   PAGINACIÓN — helper genérico
═══════════════════════════════════════════ */
function renderPaginacion(containerId, total, paginaActual, porPagina, totalPags, onPageChange, onPerPageChange) {
    const el = document.getElementById(containerId);
    if (!el) return;

    const desde = total === 0 ? 0 : (paginaActual - 1) * porPagina + 1;
    const hasta  = Math.min(paginaActual * porPagina, total);

    const opciones = [5, 10, 25, 50].map(n =>
        `<option value="${n}" ${n === porPagina ? 'selected' : ''}>${n}</option>`
    ).join('');

    let btns = '';
    if (totalPags > 1) {
        btns += `<button class="pag-btn" ${paginaActual===1?'disabled':''} onclick="(${onPageChange})(${paginaActual-1})"><i class="bi bi-chevron-left"></i></button>`;
        const rango = 2;
        for (let i = 1; i <= totalPags; i++) {
            if (i === 1 || i === totalPags || (i >= paginaActual - rango && i <= paginaActual + rango)) {
                btns += `<button class="pag-btn ${i===paginaActual?'pag-active':''}" onclick="(${onPageChange})(${i})">${i}</button>`;
            } else if (i === paginaActual - rango - 1 || i === paginaActual + rango + 1) {
                btns += `<span class="pag-dots">…</span>`;
            }
        }
        btns += `<button class="pag-btn" ${paginaActual===totalPags?'disabled':''} onclick="(${onPageChange})(${paginaActual+1})"><i class="bi bi-chevron-right"></i></button>`;
    }

    el.innerHTML = `
        <div class="pag-left">
            <span class="pag-info">${desde}–${hasta} de ${total}</span>
            <label class="pag-label">Filas:
                <select class="pag-select" onchange="(${onPerPageChange})(parseInt(this.value))">${opciones}</select>
            </label>
        </div>
        <div class="pag-btns">${btns}</div>`;
}

/* ═══════════════════════════════════════════
   PAGINACIÓN — Historial
═══════════════════════════════════════════ */
let histPagina   = 1;
let histPorPagina = 10;
let histDataCache = [];

function renderHistorial(data) {
    histDataCache = data;
    const totalPags = Math.max(1, Math.ceil(data.length / histPorPagina));
    if (histPagina > totalPags) histPagina = totalPags;
    const slice = data.slice((histPagina - 1) * histPorPagina, histPagina * histPorPagina);

    const tbody = document.getElementById('histBody');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--eco-muted)">Sin movimientos registrados.</td></tr>';
        renderPaginacion('histPaginacion', 0, 1, histPorPagina, 1, ()=>{}, ()=>{});
        return;
    }
    tbody.innerHTML = slice.map(m => {
        const ent = m.direccion === 'entrada';
        const tipoBadge = m.tipo === 'bizum'
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

    renderPaginacion('histPaginacion', data.length, histPagina, histPorPagina, totalPags,
        p => { histPagina = p; renderHistorial(histDataCache); },
        n => { histPorPagina = n; histPagina = 1; renderHistorial(histDataCache); }
    );
}