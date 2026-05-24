<!-- ════════ MOVIMIENTOS ADMIN ════════ -->
<section class="page-section" id="sec-movimientos">
    <div class="section-header">
        <h2>Movimientos</h2>
        <p>Registro completo de todas las transacciones realizadas en el sistema.</p>
    </div>

    <div class="stat-grid mb-4" id="adminMovStatsGrid">
        <div class="stat-card primary-card">
            <div class="stat-label">Total transacciones</div>
            <div class="stat-value" id="adminMovTotal">—</div>
            <div class="stat-meta"><span class="positive" id="adminMovMes">— este mes</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Volumen bizum</div>
            <div class="stat-value" id="adminMovVolBizum">—</div>
            <div class="stat-meta"><span style="color:var(--eco-muted)">Acumulado total</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Volumen transferencias</div>
            <div class="stat-value" id="adminMovVolTrans">—</div>
            <div class="stat-meta"><span style="color:var(--eco-muted)">Acumulado total</span></div>
        </div>
    </div>

    <div class="eco-card mb-4">
        <div class="card-header-eco" style="padding-bottom:16px">
            <div>
                <div class="card-title-eco">Filtros</div>
                <div class="card-subtitle-eco">Filtra por tipo o estado</div>
            </div>
        </div>
        <div style="padding:0 24px 20px;display:flex;gap:12px;flex-wrap:wrap">
            <select id="adminFiltroTipo" class="eco-input" style="width:auto;min-width:160px" onchange="filtrarMovimientosAdmin()">
                <option value="">Todos los tipos</option>
                <option value="bizum">Bizum</option>
                <option value="transferencia">Transferencia</option>
            </select>
            <select id="adminFiltroEstado" class="eco-input" style="width:auto;min-width:160px" onchange="filtrarMovimientosAdmin()">
                <option value="">Todos los estados</option>
                <option value="completada">Completada</option>
                <option value="pendiente">Pendiente</option>
                <option value="rechazada">Rechazada</option>
            </select>
        </div>
    </div>

    <div class="eco-card">
        <div style="overflow-x:auto">
            <table class="hist-table" id="tablaAdminMovimientos">
                <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Concepto / Destinatario</th>
                    <th>Cuenta origen</th>
                    <th>Importe</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody id="tablaAdminMovimientosTbody">
                <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--eco-muted)">Cargando movimientos...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="movimientosPaginacion" class="pag-container"></div>
    </div>
</section>