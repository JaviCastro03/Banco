        <!-- ════════ INICIO ════════ -->
        <section class="page-section active" id="sec-inicio">
            <div class="section-header">
                <h2>Bienvenido, <span style="color:var(--eco-primary)" id="welcomeName"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span></h2>
                <p>Aquí tienes un resumen de tu actividad reciente.</p>
            </div>

            <div class="stat-grid" id="statGrid">
                <div class="stat-card primary-card">
                    <div class="stat-label">Saldo disponible</div>
                    <div class="stat-value" id="statSaldo">—</div>
                    <div class="stat-meta"><span class="positive" id="statIban">Cargando...</span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Enviado este mes</div>
                    <div class="stat-value" id="statEnviado">—</div>
                    <div class="stat-meta"><span id="statNumEnv">—</span> transferencias</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Recibido este mes</div>
                    <div class="stat-value" id="statRecibido">—</div>
                    <div class="stat-meta"><span style="color:var(--eco-muted)">Nómina + bizums</span></div>
                </div>
            </div>

            <div class="section-header mt-2 mb-3">
                <strong style="font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:var(--eco-muted)">Acciones rápidas</strong>
            </div>
            <div class="quick-grid mb-4">
                <div class="quick-btn primary" onclick="showSection('bizum',null)">
                    <div class="quick-btn-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                    <div>
                        <div class="quick-btn-title">Enviar Bizum</div>
                        <div class="quick-btn-sub">Transferencia instantánea</div>
                    </div>
                </div>
                <div class="quick-btn" onclick="showSection('transferencia',null)">
                    <div class="quick-btn-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <div>
                        <div class="quick-btn-title">Nueva transferencia</div>
                        <div class="quick-btn-sub">SEPA · nacional · internacional</div>
                    </div>
                </div>
                <div class="quick-btn" onclick="showSection('historial',null)">
                    <div class="quick-btn-icon"><i class="bi bi-list-ul"></i></div>
                    <div>
                        <div class="quick-btn-title">Ver extracto</div>
                        <div class="quick-btn-sub">Movimientos y descargas</div>
                    </div>
                </div>
                <div class="quick-btn" onclick="showSection('tarjeta',null)">
                    <div class="quick-btn-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
                    <div>
                        <div class="quick-btn-title">Mi tarjeta</div>
                        <div class="quick-btn-sub">Datos y gestión</div>
                    </div>
                </div>
            </div>

            <div class="eco-card">
                <div class="card-header-eco">
                    <div>
                        <div class="card-title-eco">Últimos movimientos</div>
                        <div class="card-subtitle-eco">Actividad reciente de tu cuenta</div>
                    </div>
                    <button class="eco-btn eco-btn-outline eco-btn-sm" onclick="showSection('historial',null)">Ver todo</button>
                </div>
                <ul class="mov-list" id="lastMovList">
                    <li class="mov-item"><div style="color:var(--eco-muted);font-size:13px;padding:8px 0">Cargando movimientos...</div></li>
                </ul>
            </div>
        </section>
