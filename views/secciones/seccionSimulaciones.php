        <!-- ════════ SIMULACIONES ════════ -->
        <section class="page-section" id="sec-simulaciones">
            <div class="section-header">
                <h2>Simulación de <span style="color:var(--eco-primary)">Ingresos y Gastos</span></h2>
                <p>Simula suscripciones, facturas y tu nómina para ver cómo afectan a tu saldo.</p>
            </div>

            <!-- Botón procesar -->
            <div class="d-flex justify-content-end mb-3">
                <button class="eco-btn eco-btn-primary" onclick="procesarSimulaciones()">
                    <i class="bi bi-play-circle-fill"></i> Procesar simulaciones pendientes
                </button>
            </div>

            <div id="alertSim" class="mb-3"></div>

            <div class="row g-4">
                <!-- GASTOS -->
                <div class="col-lg-6">
                    <div class="eco-card p-4 h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-arrow-down-circle-fill fs-4" style="color:#e63946"></i>
                            <h5 class="m-0 fw-bold">Gastos periódicos</h5>
                        </div>

                        <!-- Plantillas rápidas de gastos -->
                        <div class="mb-3">
                            <p style="font-size:12px;color:var(--eco-muted);text-transform:uppercase;font-weight:600;letter-spacing:1px">Añadir rápido</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="sim-quick-btn" onclick="quickGasto('Spotify',10,1)"><i class="bi bi-music-note-beamed"></i> Spotify <small>€10/día</small></button>
                                <button class="sim-quick-btn" onclick="quickGasto('Luz',0,1)"><i class="bi bi-lightning-charge-fill"></i> Luz <small>random</small></button>
                                <button class="sim-quick-btn" onclick="quickGasto('Agua',0,1)"><i class="bi bi-droplet-fill"></i> Agua <small>random</small></button>
                            </div>
                        </div>

                        <!-- Formulario gasto personalizado -->
                        <div class="sim-form-box" style="background:#fff5f5;border:1.5px solid #f5c6cb;border-radius:10px;padding:14px;margin-bottom:16px">
                            <p style="font-size:11px;font-weight:700;color:#c0392b;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px"><i class="bi bi-plus-circle"></i> Gasto personalizado</p>
                            <input type="text" class="eco-input mb-2" id="gastoNombre" placeholder="Nombre (ej: Netflix, Gym...)">
                            <div class="d-flex gap-2">
                                <input type="number" class="eco-input" id="gastoImporte" placeholder="€ importe" min="0.01" step="0.01">
                                <select class="eco-input" id="gastoIntervalo" style="width:auto;flex:1">
                                    <option value="1">Cada día</option>
                                    <option value="7">Cada semana</option>
                                    <option value="30" selected>Cada mes</option>
                                </select>
                            </div>
                            <button class="eco-btn eco-btn-primary w-100 mt-2" onclick="crearSimulacion('gasto')">
                                <i class="bi bi-plus-lg"></i> Añadir gasto
                            </button>
                        </div>

                        <div id="listaGastos" class="sim-lista"></div>
                    </div>
                </div>

                <!-- INGRESOS -->
                <div class="col-lg-6">
                    <div class="eco-card p-4 h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-arrow-up-circle-fill fs-4" style="color:#2d6a4f"></i>
                            <h5 class="m-0 fw-bold">Ingresos periódicos</h5>
                        </div>

                        <!-- Plantilla nómina -->
                        <div class="mb-3">
                            <p style="font-size:12px;color:var(--eco-muted);text-transform:uppercase;font-weight:600;letter-spacing:1px">Nómina</p>
                            <div class="sim-nomina-box eco-card p-3" style="background:#f0f7f4;border:1.5px solid var(--eco-pale)">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-briefcase-fill" style="color:var(--eco-primary)"></i>
                                    <span style="font-weight:600;font-size:14px">Configurar nómina</span>
                                </div>
                                <p style="font-size:12px;color:var(--eco-muted);margin-bottom:10px">Se ingresará automáticamente cada 2 días como simulación.</p>
                                <input type="number" class="eco-input mb-2" id="nominaImporte" placeholder="€ importe nómina" min="0.01" step="0.01">
                                <button class="eco-btn eco-btn-primary w-100" onclick="crearNomina()">
                                    <i class="bi bi-cash-coin"></i> Activar nómina
                                </button>
                            </div>
                        </div>

                        <!-- Ingreso personalizado -->
                        <div class="sim-form-box" style="background:#f0f7f4;border:1.5px solid var(--eco-pale);border-radius:10px;padding:14px;margin-bottom:16px">
                            <p style="font-size:11px;font-weight:700;color:var(--eco-primary);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px"><i class="bi bi-plus-circle"></i> Ingreso personalizado</p>
                            <input type="text" class="eco-input mb-2" id="ingresoNombre" placeholder="Nombre (ej: Alquiler local...)">
                            <div class="d-flex gap-2">
                                <input type="number" class="eco-input" id="ingresoImporte" placeholder="€ importe" min="0.01" step="0.01">
                                <select class="eco-input" id="ingresoIntervalo" style="width:auto;flex:1">
                                    <option value="1">Cada día</option>
                                    <option value="2">Cada 2 días</option>
                                    <option value="7">Cada semana</option>
                                    <option value="30" selected>Cada mes</option>
                                </select>
                            </div>
                            <button class="eco-btn eco-btn-primary w-100 mt-2" onclick="crearSimulacion('ingreso')">
                                <i class="bi bi-plus-lg"></i> Añadir ingreso
                            </button>
                        </div>

                        <div id="listaIngresos" class="sim-lista"></div>
                    </div>
                </div>
            </div>
        </section>

<style>
.sim-quick-btn {
    background: var(--eco-pale);
    border: 1.5px solid var(--eco-light);
    color: var(--eco-dark);
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all .2s;
    font-family: inherit;
}
.sim-quick-btn:hover { background: var(--eco-light); color: #fff; }
.sim-quick-btn small { color: var(--eco-muted); font-weight: 400; }
.sim-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    border-radius: 10px;
    background: #f8faf9;
    border: 1px solid var(--eco-border);
    margin-bottom: 8px;
    gap: 10px;
}
.sim-item.inactiva { opacity: .55; }
.sim-item-info { flex: 1; min-width: 0; }
.sim-item-nombre { font-weight: 700; font-size: 14px; color: var(--eco-text); }
.sim-item-detalle { font-size: 11px; color: var(--eco-muted); }
.sim-item-importe { font-weight: 700; font-size: 15px; white-space: nowrap; }
.sim-item-importe.gasto { color: #e63946; }
.sim-item-importe.ingreso { color: #2d6a4f; }
.sim-actions { display: flex; gap: 6px; }
.sim-btn { background: none; border: 1px solid var(--eco-border); border-radius: 7px; padding: 5px 8px; cursor: pointer; font-size: 13px; transition: all .2s; }
.sim-btn:hover { background: var(--eco-pale); }
.sim-btn.danger:hover { background: #fde8ea; border-color: #f5a3a9; color: #e63946; }
</style>
