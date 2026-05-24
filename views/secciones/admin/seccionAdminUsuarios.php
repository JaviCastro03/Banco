<!-- ════════ USUARIOS ADMIN ════════ -->
<section class="page-section" id="sec-usuarios">
    <div class="section-header">
        <h2>Usuarios</h2>
        <p>Listado completo de usuarios registrados en el sistema.</p>
    </div>

    <div class="eco-card mb-4">
        <div class="card-header-eco" style="padding-bottom:16px">
            <div>
                <div class="card-title-eco">Buscar y filtrar</div>
                <div class="card-subtitle-eco">Filtra por nombre, apellidos o email</div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span id="adminUsuariosCount" style="font-size:13px;color:var(--eco-muted)">— usuarios</span>
            </div>
        </div>
        <div style="padding:0 24px 20px">
            <div class="position-relative">
                <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--eco-muted);font-size:14px;pointer-events:none"></i>
                <input type="text" id="adminBuscador" class="eco-input" placeholder="Buscar por nombre o email..." style="padding-left:40px"
                       oninput="buscarUsuariosAdmin(this.value)">
            </div>
        </div>
    </div>

    <div class="eco-card">
        <div style="overflow-x:auto">
            <table class="hist-table" id="tablaAdminUsuarios">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody id="tablaAdminUsuariosTbody">
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--eco-muted)">Cargando usuarios...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="usuariosPaginacion" class="pag-container"></div>
    </div>
</section>