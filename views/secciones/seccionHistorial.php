<!-- ════════ HISTORIAL ════════ -->
<section class="page-section" id="sec-historial">
    <div class="section-header d-flex justify-content-between align-items-start">
        <div>
            <h2>Historial de <span style="color:var(--eco-primary)">Movimientos</span></h2>
            <p>Todos tus ingresos y gastos en un solo lugar.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <select class="eco-input" style="width:auto;font-size:13px;padding:9px 14px" id="filtroTipo" onchange="filtrarHistorial()">
                <option value="">Todos los tipos</option>
                <option value="bizum">Bizum</option>
                <option value="transferencia">Transferencia</option>
            </select>
            <select class="eco-input" style="width:auto;font-size:13px;padding:9px 14px" id="filtroDir" onchange="filtrarHistorial()">
                <option value="">Todas las direcciones</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
            </select>
        </div>
    </div>
    <div class="eco-card">
        <div style="overflow-x:auto">
            <table class="hist-table" id="histTable">
                <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Concepto / Destinatario</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Importe</th>
                </tr>
                </thead>
                <tbody id="histBody">
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--eco-muted)"><div class="spinner-border spinner-border-sm"></div> Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="histPaginacion" class="pag-container"></div>
    </div>
</section>