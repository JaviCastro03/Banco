<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) {
    $_SESSION["ruta"] = "login";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBank — Panel de Administración</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./views/styles/dashboard.css">
    <link rel="stylesheet" href="./views/styles/admin.css">
</head>
<body>

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><img src="views/styles/logo.png" alt="Logo EcoBank" style="width:100%; height:100%; object-fit:contain;"></div>
        <div>
            <div class="logo-text">EcoBank</div>
            <div class="logo-sub">Administración</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Panel</div>
        <div class="nav-item active" data-section="resumen" onclick="showAdminSection('resumen',this)">
            <i class="bi bi-grid-1x2-fill"></i><span>Resumen</span>
        </div>

        <div class="nav-section-label">Gestión</div>
        <div class="nav-item" data-section="usuarios" onclick="showAdminSection('usuarios',this)">
            <i class="bi bi-people-fill"></i><span>Usuarios</span>
        </div>
        <div class="nav-item" data-section="cuentas" onclick="showAdminSection('cuentas',this)">
            <i class="bi bi-bank2"></i><span>Cuentas</span>
        </div>
        <div class="nav-item" data-section="movimientos" onclick="showAdminSection('movimientos',this)">
            <i class="bi bi-arrow-left-right"></i><span>Movimientos</span>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar admin-avatar" id="sidebarAvatar">
                <?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>
                <div class="user-role admin-role-label">Administrador</div>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-left"></i> Cerrar sesión
        </a>
    </div>
</aside>

<!-- ══════════════ MAIN ══════════════ -->
<div class="main-content">
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="hamburger" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
            <div class="topbar-title" id="topbarTitle">Resumen <span>/ Panel de administración</span></div>
        </div>
        <div class="topbar-actions">
            <div class="admin-badge">
                <i class="bi bi-shield-fill-check"></i> Admin
            </div>
            <div class="user-avatar admin-avatar" style="cursor:default" id="topAvatar">
                <?= strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        <?php include __DIR__ . '/secciones/admin/seccionAdminResumen.php'; ?>
        <?php include __DIR__ . '/secciones/admin/seccionAdminUsuarios.php'; ?>
        <?php include __DIR__ . '/secciones/admin/seccionAdminCuentas.php'; ?>
        <?php include __DIR__ . '/secciones/admin/seccionAdminMovimientos.php'; ?>
    </main>
</div>

<!-- ══════ MODAL EDITAR USUARIO ══════ -->
<div class="modal-overlay" id="modalEditarUsuario">
    <div class="modal-box" style="max-width:520px">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0">Editar usuario</h5>
            <button class="btn-copy" onclick="cerrarModalAdmin('modalEditarUsuario')"><i class="bi bi-x-lg"></i></button>
        </div>
        <input type="hidden" id="editUserId">
        <div class="row g-3">
            <div class="col-6">
                <label class="eco-label">Nombre</label>
                <input type="text" class="eco-input" id="editNombre">
            </div>
            <div class="col-6">
                <label class="eco-label">Apellidos</label>
                <input type="text" class="eco-input" id="editApellidos">
            </div>
            <div class="col-12">
                <label class="eco-label">Email</label>
                <input type="email" class="eco-input" id="editEmail">
            </div>
            <div class="col-6">
                <label class="eco-label">Teléfono</label>
                <input type="text" class="eco-input" id="editTelefono">
            </div>
            <div class="col-6">
                <label class="eco-label">Rol</label>
                <select class="eco-input" id="editRol">
                    <option value="0">No verificado</option>
                    <option value="1">Usuario</option>
                    <option value="2">Administrador</option>
                </select>
            </div>
        </div>
        <div id="alertEditarUsuario" class="mt-3"></div>
        <div class="d-flex gap-3 mt-4">
            <button class="eco-btn eco-btn-outline flex-fill" onclick="cerrarModalAdmin('modalEditarUsuario')">Cancelar</button>
            <button class="eco-btn eco-btn-primary flex-fill" onclick="guardarEdicionUsuario()">
                <i class="bi bi-floppy-fill"></i> Guardar cambios
            </button>
        </div>
    </div>
</div>

<!-- ══════ MODAL CONFIRMAR ELIMINAR ══════ -->
<div class="modal-overlay" id="modalEliminarUsuario">
    <div class="modal-box" style="max-width:420px">
        <div class="text-center mb-3">
            <div style="width:56px;height:56px;border-radius:50%;background:#fde8ea;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px;color:#e63946">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h5>Eliminar usuario</h5>
            <p style="font-size:13px;color:var(--eco-muted);margin-top:8px">Esta acción desactivará la cuenta del usuario. Sus datos y transacciones se conservarán.</p>
        </div>
        <input type="hidden" id="eliminarUserId">
        <div id="alertEliminarUsuario" class="mt-2"></div>
        <div class="d-flex gap-3 mt-4">
            <button class="eco-btn eco-btn-outline flex-fill" onclick="cerrarModalAdmin('modalEliminarUsuario')">Cancelar</button>
            <button class="eco-btn eco-btn-danger flex-fill" onclick="confirmarEliminarUsuario()">
                <i class="bi bi-trash3-fill"></i> Eliminar
            </button>
        </div>
    </div>
</div>

<!-- ══════ MODAL HISTORIAL DE USUARIO ══════ -->
<div class="modal-overlay" id="modalHistorialUsuario">
    <div class="modal-box" style="max-width:720px;width:95vw">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="mb-0" id="modalHistTitulo">Movimientos del usuario</h5>
                <div style="font-size:12px;color:var(--eco-muted);margin-top:3px" id="modalHistSub"></div>
            </div>
            <button class="btn-copy" onclick="cerrarModalAdmin('modalHistorialUsuario')"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="modalHistorialContenido">
            <div style="text-align:center;padding:32px;color:var(--eco-muted)">Cargando...</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="./views/js/admin.js"></script>
</body>
</html>
