<!-- ════════ RESUMEN ADMIN ════════ -->
<section class="page-section active" id="sec-resumen">
    <div class="section-header">
        <h2>Bienvenido, <span style="color:var(--eco-primary)"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span></h2>
        <p>Resumen general del sistema EcoBank.</p>
    </div>

    <div class="stat-grid" id="adminStatGrid">
        <div class="stat-card primary-card">
            <div class="stat-label">Total usuarios</div>
            <div class="stat-value" id="adminStatUsuarios">—</div>
            <div class="stat-meta"><span class="positive" id="adminStatVerificados">— verificados</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Cuentas activas</div>
            <div class="stat-value" id="adminStatCuentas">—</div>
            <div class="stat-meta"><span style="color:var(--eco-muted)">Cuentas corrientes</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Transacciones totales</div>
            <div class="stat-value" id="adminStatTransacciones">—</div>
            <div class="stat-meta"><span id="adminStatTransMes">— este mes</span></div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="eco-card h-100">
                <div class="card-header-eco">
                    <div>
                        <div class="card-title-eco">Accesos rapidos</div>
                        <div class="card-subtitle-eco">Navegacion directa a cada seccion</div>
                    </div>
                </div>
                <div style="padding:0 24px 24px">
                    <div class="quick-grid" style="grid-template-columns:1fr 1fr">
                        <div class="quick-btn" onclick="showAdminSection('usuarios',null)">
                            <div class="quick-btn-icon"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="quick-btn-title">Usuarios</div>
                                <div class="quick-btn-sub">Gestionar cuentas</div>
                            </div>
                        </div>
                        <div class="quick-btn" onclick="showAdminSection('cuentas',null)">
                            <div class="quick-btn-icon"><i class="bi bi-bank2"></i></div>
                            <div>
                                <div class="quick-btn-title">Cuentas</div>
                                <div class="quick-btn-sub">Saldos e IBAN</div>
                            </div>
                        </div>
                        <div class="quick-btn" onclick="showAdminSection('movimientos',null)">
                            <div class="quick-btn-icon"><i class="bi bi-arrow-left-right"></i></div>
                            <div>
                                <div class="quick-btn-title">Movimientos</div>
                                <div class="quick-btn-sub">Todas las transacciones</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="eco-card h-100">
                <div class="card-header-eco">
                    <div>
                        <div class="card-title-eco">Ultimos usuarios registrados</div>
                        <div class="card-subtitle-eco">Altas recientes en el sistema</div>
                    </div>
                    <button class="eco-btn eco-btn-outline eco-btn-sm" onclick="showAdminSection('usuarios',null)">Ver todos</button>
                </div>
                <ul class="mov-list" id="adminUltimosUsuarios">
                    <li class="mov-item"><div style="color:var(--eco-muted);font-size:13px;padding:8px 0">Cargando...</div></li>
                </ul>
            </div>
        </div>
    </div>
</section>
