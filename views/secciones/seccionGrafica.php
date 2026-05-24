<!-- ════════ GRÁFICA ════════ -->
<section class="page-section" id="sec-grafica">
    <div class="section-header">
        <h2>Análisis <span style="color:var(--eco-primary)">Financiero</span></h2>
        <p>Visualiza tus ingresos y gastos según el periodo seleccionado.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="eco-card">
                <div class="card-header-eco">
                    <div>
                        <div class="card-title-eco">Ingresos vs Gastos</div>
                        <div class="card-subtitle-eco" id="grafSubtitle">Últimos 6 meses</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="eco-btn eco-btn-sm eco-btn-outline filtro-btn" data-filtro="dias"    onclick="cambiarFiltroGrafica('dias',this)">Días</button>
                        <button class="eco-btn eco-btn-sm eco-btn-outline filtro-btn" data-filtro="semanas" onclick="cambiarFiltroGrafica('semanas',this)">Semanas</button>
                        <button class="eco-btn eco-btn-sm eco-btn-primary filtro-btn" data-filtro="meses"   onclick="cambiarFiltroGrafica('meses',this)">Meses</button>
                        <button class="eco-btn eco-btn-sm eco-btn-outline filtro-btn" data-filtro="años"    onclick="cambiarFiltroGrafica('años',this)">Años</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="eco-card p-4 mb-4">
                <div class="card-title-eco mb-3">Resumen del periodo</div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--eco-pale);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-graph-up-arrow" style="font-size:18px;color:var(--eco-primary)"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px">Recibido</div>
                        <div style="font-size:20px;font-weight:700;color:var(--eco-primary)" id="grafRecibido">—</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:#fde8ea;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-graph-down-arrow" style="font-size:18px;color:#e63946"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px">Enviado</div>
                        <div style="font-size:20px;font-weight:700;color:#e63946" id="grafEnviado">—</div>
                    </div>
                </div>
            </div>
            <div class="eco-card p-4">
                <div class="card-title-eco mb-2">Leyenda</div>
                <div class="d-flex align-items-center gap-2 mb-2"><div style="width:14px;height:14px;border-radius:3px;background:#2d6a4f"></div><span style="font-size:13px">Recibido</span></div>
                <div class="d-flex align-items-center gap-2"><div style="width:14px;height:14px;border-radius:3px;background:#e63946"></div><span style="font-size:13px">Gastado</span></div>
            </div>
        </div>
    </div>

    <!-- ═══ DESGLOSE POR TIPO ═══ -->
    <div class="row g-4 mt-1">
        <div class="col-lg-5">
            <div class="eco-card p-4">
                <div class="card-title-eco mb-1">Desglose por tipo</div>
                <div class="card-subtitle-eco mb-3">Distribución de todas tus operaciones</div>
                <div style="position:relative;height:260px;display:flex;align-items:center;justify-content:center">
                    <canvas id="chartSectores"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="eco-card p-4 h-100">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px">
                    <div>
                        <div class="card-title-eco">Resumen de operaciones</div>
                        <div class="card-subtitle-eco">Número de transacciones y volumen total</div>
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <button class="pag-btn desglose-btn" data-filtro="dias"    onclick="loadDesglose('dias')">30d</button>
                        <button class="pag-btn desglose-btn" data-filtro="semanas" onclick="loadDesglose('semanas')">12sem</button>
                        <button class="pag-btn desglose-btn" data-filtro="meses"   onclick="loadDesglose('meses')">6m</button>
                        <button class="pag-btn desglose-btn" data-filtro="años"    onclick="loadDesglose('años')">5a</button>
                        <button class="pag-btn desglose-btn pag-active" data-filtro="total" onclick="loadDesglose('total')">Todo</button>
                    </div>
                </div>
                <div id="desgloseTabla"></div>
            </div>
        </div>
    </div>
</section>