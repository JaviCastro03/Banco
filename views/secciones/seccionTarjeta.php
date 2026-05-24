<!-- ════════ TARJETA ════════ -->
<section class="page-section" id="sec-tarjeta">
    <div class="section-header">
        <h2>Mi <span style="color:var(--eco-primary)">Tarjeta</span></h2>
        <p>Gestiona tu tarjeta bancaria EcoBank.</p>
    </div>

    <div id="alertTarjeta"></div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div id="cardDisplay">
                <div class="card-empty">
                    <div class="card-empty-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h5>Sin tarjeta activa</h5>
                    <p>No tienes ninguna tarjeta asociada a tu cuenta.</p>
                    <button class="eco-btn eco-btn-primary" onclick="crearTarjeta()">
                        <i class="bi bi-plus-circle-fill"></i> Solicitar tarjeta
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="eco-card p-4">
                <h6 class="fw-bold mb-3">Información de seguridad</h6>

                <div class="d-flex gap-3 mb-3 align-items-start">
                    <i class="bi bi-shield-lock-fill fs-5" style="color:var(--eco-primary);margin-top:2px"></i>
                    <div>
                        <strong style="font-size:13px">Número protegido</strong>
                        <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">
                            El número completo de tu tarjeta está cifrado. Solo se muestra parcialmente.
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3 align-items-start">
                    <i class="bi bi-eye-slash-fill fs-5" style="color:var(--eco-primary);margin-top:2px"></i>
                    <div>
                        <strong style="font-size:13px">Datos protegidos</strong>
                        <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">
                            Puedes consultar los datos completos desde el botón "Ver datos".
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-3 align-items-start">
                    <i class="bi bi-calendar-check-fill fs-5" style="color:var(--eco-primary);margin-top:2px"></i>
                    <div>
                        <strong style="font-size:13px">Renovación automática</strong>
                        <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">
                            Tu tarjeta se renueva automáticamente antes de su vencimiento.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL DATOS TARJETA -->
<div class="modal-overlay" id="modalTarjetaDatos">
    <div class="modal-box">
        <button onclick="cerrarModal('modalTarjetaDatos')" class="modal-close">
            <i class="bi bi-x-lg"></i>
        </button>
        <h5>
            <i class="bi bi-credit-card-fill"></i> Datos de la tarjeta
        </h5>

        <div id="modalTarjetaBody"></div>

        <div class="mt-3 text-end">
            <button class="eco-btn eco-btn-outline" onclick="cerrarModal('modalTarjetaDatos')">
                Cerrar
            </button>
        </div>
    </div>
</div>