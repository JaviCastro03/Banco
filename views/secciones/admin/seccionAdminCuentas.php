<!-- ════════ CUENTAS ADMIN ════════ -->
<section class="page-section" id="sec-cuentas">
    <div class="section-header">
        <h2>Cuentas bancarias</h2>
        <p>Consulta el estado y los detalles de todas las cuentas del sistema.</p>
    </div>

    <div class="eco-card mb-4">
        <div class="card-header-eco" style="padding-bottom:16px">
            <div>
                <div class="card-title-eco">Buscar cuenta</div>
                <div class="card-subtitle-eco">Filtra por titular o IBAN</div>
            </div>
            <span id="adminCuentasCount" style="font-size:13px;color:var(--eco-muted)">— cuentas</span>
        </div>
        <div style="padding:0 24px 20px">
            <div class="position-relative">
                <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--eco-muted);font-size:14px;pointer-events:none"></i>
                <input type="text" id="adminBuscadorCuentas" class="eco-input" placeholder="Buscar por titular o IBAN..." style="padding-left:40px"
                    oninput="filtrarCuentasAdmin(this.value)">
            </div>
        </div>
    </div>

    <div class="eco-card">
        <div style="overflow-x:auto">
            <table class="hist-table" id="tablaAdminCuentas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titular</th>
                        <th>IBAN</th>
                        <th>Tipo</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th>Movimientos</th>
                    </tr>
                </thead>
                <tbody id="tablaAdminCuentasTbody">
                    <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--eco-muted)">Cargando cuentas...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
