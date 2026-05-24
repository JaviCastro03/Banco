<!-- ════════ TRANSFERENCIA ════════ -->
<section class="page-section" id="sec-transferencia">
    <div class="section-header">
        <h2>Nueva <span style="color:var(--eco-primary)">Transferencia</span></h2>
        <p>Transfiere dinero a cualquier cuenta bancaria. Requiere verificación por email.</p>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="eco-card p-4">
                <div id="alertTrans"></div>
                <div class="eco-form-group">
                    <label class="eco-label">IBAN de destino</label>
                    <input type="text" class="eco-input" id="transIBAN" placeholder="ES00 0000 0000 0000 0000 0000" maxlength="34" style="letter-spacing:1px;text-transform:uppercase">
                </div>
                <div class="eco-form-group">
                    <label class="eco-label">Titular de la cuenta</label>
                    <input type="text" class="eco-input" id="transTitular" placeholder="Nombre del beneficiario">
                </div>
                <div class="eco-form-group">
                    <label class="eco-label">Importe (€)</label>
                    <input type="number" class="eco-input" id="transImporte" placeholder="0,00" min="0.01" step="0.01">
                </div>
                <div class="eco-form-group">
                    <label class="eco-label">Concepto</label>
                    <input type="text" class="eco-input" id="transConcepto" placeholder="Ej: Alquiler mayo" maxlength="255">
                </div>
                <button class="eco-btn eco-btn-primary w-100" id="btnEnviarTrans" onclick="solicitarCodigoTrans()">
                    <i class="bi bi-arrow-right-circle-fill"></i> Realizar transferencia
                </button>

                <!-- CONTACTOS RECIENTES -->
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clock-history"></i> Transferencias recientes
                    </h6>
                    <div id="transRecientes" class="d-flex flex-column gap-2">
                        <span style="font-size:12px;color:var(--eco-muted)">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="eco-card p-4">
                <div class="text-center mb-3"><i class="bi bi-bank fs-2" style="color:var(--eco-primary)"></i></div>
                <h6 class="fw-bold mb-2">Información importante</h6>
                <p style="font-size:13px;color:var(--eco-muted);line-height:1.6">
                    Las transferencias SEPA a otras entidades pueden tardar <strong>1-2 días hábiles</strong>. Para cuentas internas de EcoBank el saldo se actualiza al instante.<br><br>
                    Para importes superiores a <strong>€500</strong> recibirás un código de verificación adicional en tu email.<br><br>
                    <span style="color:#e63946"><i class="bi bi-exclamation-triangle-fill"></i> Verifica siempre el IBAN antes de confirmar.</span>
                </p>
            </div>
        </div>
    </div>
</section>