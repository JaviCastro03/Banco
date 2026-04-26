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
        </section>
