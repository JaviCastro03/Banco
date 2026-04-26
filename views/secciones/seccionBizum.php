        <!-- ════════ BIZUM ════════ -->
        <section class="page-section" id="sec-bizum">
            <div class="section-header">
                <h2>Enviar <span style="color:var(--eco-primary)">Bizum</span></h2>
                <p>Envía dinero al instante a cualquier teléfono registrado en EcoBank.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="eco-card p-4">
                        <div id="alertBizum"></div>
                        <div class="eco-form-group">
                            <label class="eco-label">Teléfono del destinatario</label>
                            <input type="tel" class="eco-input" id="bizumTel" placeholder="Ej: 612 345 678" maxlength="15">
                        </div>
                        <div class="eco-form-group">
                            <label class="eco-label">Importe (€)</label>
                            <input type="number" class="eco-input" id="bizumImporte" placeholder="0,00" min="0.01" step="0.01" max="1000">
                        </div>
                        <div class="eco-form-group">
                            <label class="eco-label">Concepto (opcional)</label>
                            <input type="text" class="eco-input" id="bizumConcepto" placeholder="Ej: Cena del viernes" maxlength="140">
                        </div>
                        <button class="eco-btn eco-btn-primary w-100" id="btnEnviarBizum" onclick="solicitarCodigoBizum()">
                            <i class="bi bi-lightning-charge-fill"></i> Enviar Bizum
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="eco-card p-4 h-100">
                        <div class="text-center mb-3"><i class="bi bi-shield-check fs-2 text-success"></i></div>
                        <h6 class="fw-bold mb-3">¿Cómo funciona?</h6>
                        <div class="d-flex gap-3 mb-3 align-items-start">
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--eco-pale);display:flex;align-items:center;justify-content:center;color:var(--eco-primary);font-weight:700;flex-shrink:0;font-size:13px">1</div>
                            <div><strong style="font-size:13px">Introduce el teléfono</strong><p style="font-size:12px;color:var(--eco-muted);margin:2px 0 0">El destinatario debe ser cliente de EcoBank.</p></div>
                        </div>
                        <div class="d-flex gap-3 mb-3 align-items-start">
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--eco-pale);display:flex;align-items:center;justify-content:center;color:var(--eco-primary);font-weight:700;flex-shrink:0;font-size:13px">2</div>
                            <div><strong style="font-size:13px">Introduce el código</strong><p style="font-size:12px;color:var(--eco-muted);margin:2px 0 0">Te enviaremos un código de 5 dígitos a tu email.</p></div>
                        </div>
                        <div class="d-flex gap-3 align-items-start">
                            <div style="width:28px;height:28px;border-radius:50%;background:var(--eco-pale);display:flex;align-items:center;justify-content:center;color:var(--eco-primary);font-weight:700;flex-shrink:0;font-size:13px">3</div>
                            <div><strong style="font-size:13px">¡Listo!</strong><p style="font-size:12px;color:var(--eco-muted);margin:2px 0 0">El dinero llega al instante.</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
