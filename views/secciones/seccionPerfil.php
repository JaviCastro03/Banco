        <!-- ════════ PERFIL ════════ -->
        <section class="page-section" id="sec-perfil">
            <div class="section-header">
                <h2>Mi <span style="color:var(--eco-primary)">Perfil</span></h2>
                <p>Gestiona tus datos personales y seguridad.</p>
            </div>
            <div id="alertPerfil"></div>
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="eco-card p-4 mb-4">
                        <div class="d-flex align-items-center gap-4 mb-4 pb-4" style="border-bottom:1px solid var(--eco-border)">
                            <div class="perfil-avatar-lg" id="perfilAvatar">?</div>
                            <div>
                                <h5 class="mb-1" id="perfilNombreH">—</h5>
                                <div style="font-size:13px;color:var(--eco-muted)" id="perfilEmailH">—</div>
                                <div class="mt-2">
                                    <span style="background:var(--eco-pale);color:var(--eco-primary);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">Cliente verificado <i class="bi bi-check-circle-fill"></i></span>
                                </div>
                            </div>
                        </div>
                        <h6 class="fw-bold mb-3">Datos personales</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="eco-label">Nombre</label>
                                <input type="text" class="eco-input" id="pNombre">
                            </div>
                            <div class="col-6">
                                <label class="eco-label">Apellidos</label>
                                <input type="text" class="eco-input" id="pApellidos">
                            </div>
                            <div class="col-6">
                                <label class="eco-label">Email</label>
                                <input type="email" class="eco-input" id="pEmail">
                            </div>
                            <div class="col-6">
                                <label class="eco-label">Teléfono</label>
                                <input type="tel" class="eco-input" id="pTelefono">
                            </div>
                            <div class="col-6">
                                <label class="eco-label">Provincia</label>
                                <input type="text" class="eco-input" id="pProvincia">
                            </div>
                            <div class="col-6">
                                <label class="eco-label">Municipio</label>
                                <input type="text" class="eco-input" id="pMunicipio">
                            </div>
                            <div class="col-8">
                                <label class="eco-label">Vía</label>
                                <input type="text" class="eco-input" id="pVia">
                            </div>
                            <div class="col-4">
                                <label class="eco-label">Nº/Piso</label>
                                <input type="text" class="eco-input" id="pNumPiso">
                            </div>
                        </div>
                        <button class="eco-btn eco-btn-primary mt-4" onclick="guardarPerfil()"><i class="bi bi-check2-circle"></i> Guardar cambios</button>
                    </div>

                    <div class="eco-card p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-lock-fill me-1"></i> Cambiar contraseña</h6>
                        <div id="alertPass"></div>
                        <div class="eco-form-group">
                            <label class="eco-label">Contraseña actual</label>
                            <input type="password" class="eco-input" id="passActual" placeholder="••••••••">
                        </div>
                        <div class="eco-form-group">
                            <label class="eco-label">Nueva contraseña</label>
                            <input type="password" class="eco-input" id="passNueva" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="eco-form-group mb-3">
                            <label class="eco-label">Repetir nueva contraseña</label>
                            <input type="password" class="eco-input" id="passRepeat" placeholder="••••••••">
                        </div>
                        <button class="eco-btn eco-btn-outline" onclick="cambiarPass()"><i class="bi bi-lock-fill"></i> Actualizar contraseña</button>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="eco-card p-4 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-bank me-1"></i> Datos bancarios</h6>
                        <div class="eco-label mb-2">IBAN</div>
                        <div class="iban-box mb-3">
                            <span id="perfilIBAN" style="font-size:13px">—</span>
                            <button class="btn-copy" onclick="copiarIban()" title="Copiar IBAN"><i class="bi bi-copy"></i></button>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="eco-label mb-1">Tipo de cuenta</div>
                                <div style="font-size:14px;font-weight:500" id="perfilTipoCuenta">—</div>
                            </div>
                            <div class="col-6">
                                <div class="eco-label mb-1">Moneda</div>
                                <div style="font-size:14px;font-weight:500">EUR €</div>
                            </div>
                        </div>
                    </div>
                    <div class="eco-card p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle-fill me-1"></i> Información de cuenta</h6>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--eco-border)">
                            <span style="font-size:13px;color:var(--eco-muted)">DNI</span>
                            <span style="font-size:13px;font-weight:500" id="perfilDNI">—</span>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--eco-border)">
                            <span style="font-size:13px;color:var(--eco-muted)">Miembro desde</span>
                            <span style="font-size:13px;font-weight:500" id="perfilDesde">—</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span style="font-size:13px;color:var(--eco-muted)">Estado</span>
                            <span style="font-size:13px;font-weight:600;color:var(--eco-primary)"><i class="bi bi-check-circle-fill me-1"></i> Activo</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
