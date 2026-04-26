<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION["idU"])) { $_SESSION["ruta"]="login"; header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBank — Panel de Usuario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./views/styles/dashboard.css">
</head>
<body>

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><img src="views/styles/logo.png" alt="Logo EcoBank" style="width:100%; height:100%; object-fit:contain;"></div>        <div>
        <div class="logo-icon"><img src="views/styles/logo.png" alt="Logo EcoBank" style="width:100%; height:100%; object-fit:contain;"></div>
        <div>
            <div class="logo-text">EcoBank</div>
            <div class="logo-sub">Banca Sostenible</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <div class="nav-item active" data-section="inicio" onclick="showSection('inicio',this)">
            <i class="bi bi-grid-1x2-fill"></i><span>Inicio</span>
        </div>
        <div class="nav-item" data-section="bizum" onclick="showSection('bizum',this)">
            <i class="bi bi-lightning-charge-fill"></i><span>Enviar Bizum</span>
        </div>
        <div class="nav-item" data-section="transferencia" onclick="showSection('transferencia',this)">
            <i class="bi bi-arrow-left-right"></i><span>Transferencia</span>
        </div>

        <div class="nav-section-label">Mi cuenta</div>
        <div class="nav-item" data-section="historial" onclick="showSection('historial',this)">
            <i class="bi bi-clock-history"></i><span>Historial</span>
        </div>
        <div class="nav-item" data-section="grafica" onclick="showSection('grafica',this)">
            <i class="bi bi-bar-chart-line-fill"></i><span>Análisis</span>
        </div>
        <div class="nav-item" data-section="tarjeta" onclick="showSection('tarjeta',this)">
            <i class="bi bi-credit-card-2-front-fill"></i><span>Mi Tarjeta</span>
        </div>
        <div class="nav-item" data-section="perfil" onclick="showSection('perfil',this)">
            <i class="bi bi-person-circle"></i><span>Mi Perfil</span>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user" onclick="showSection('perfil',null)">
            <div class="user-avatar" id="sidebarAvatar">??</div>
            <div>
                <div class="user-name" id="sidebarName"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></div>
                <div class="user-role">Cuenta personal</div>
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
            <div class="topbar-title" id="topbarTitle">Inicio <span>/ Dashboard</span></div>
        </div>
        <div class="topbar-actions">
            <button class="btn-notif" id="btnNotif" onclick="toggleNotif()">
                <i class="bi bi-bell-fill"></i>
                <span class="notif-badge" id="notifBadge">0</span>
            </button>
            <div class="user-avatar" style="cursor:pointer" onclick="showSection('perfil',null)" id="topAvatar">??</div>
        </div>
    </header>

    <!-- NOTIFICACIONES PANEL -->
    <div class="notif-panel" id="notifPanel">
        <div class="notif-header">
            <h6><i class="bi bi-bell-fill me-2"></i>Notificaciones</h6>
            <button class="btn-copy" onclick="marcarLeidas()" title="Marcar como leídas"><i class="bi bi-check2-all"></i></button>
        </div>
        <div id="notifList"><div class="notif-empty"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>Sin notificaciones</div></div>
    </div>

    <!-- PAGE CONTENT -->
    <main class="page-content">

        <!-- ════════ INICIO ════════ -->
        <?php include __DIR__ . '/secciones/seccionInicio.php'; ?>
        <!-- ════════ BIZUM ════════ -->
        <?php include __DIR__ . '/secciones/seccionBizum.php'; ?>

        <!-- ════════ TRANSFERENCIA ════════ -->
        <?php include __DIR__ . '/secciones/seccionTransferencia.php'; ?>

        <!-- ════════ HISTORIAL ════════ -->
        <?php include __DIR__ . '/secciones/seccionHistorial.php'; ?>

        <!-- ════════ GRÁFICA ════════ -->
        <?php include __DIR__ . '/secciones/seccionGrafica.php'; ?>

        <!-- ════════ TARJETA ════════ -->
        <?php include __DIR__ . '/secciones/seccionTarjeta.php'; ?>

        <!-- ════════ PERFIL ════════ -->
        <?php include __DIR__ . '/secciones/seccionPerfil.php'; ?>

    </main><!-- /page-content -->
</div><!-- /main-content -->

<!-- ══════ MODAL CÓDIGO BIZUM ══════ -->
<div class="modal-overlay" id="modalBizum">
    <div class="modal-box">
        <div class="text-center mb-2" style="font-size:40px"><i class="bi bi-envelope-fill" style="font-size:40px;color:var(--eco-primary)"></i></div>
        <h5 class="text-center">Código de verificación</h5>
        <p class="text-center">Hemos enviado un código de 5 dígitos a tu email. Introdúcelo para confirmar el Bizum.</p>
        <div class="code-inputs" id="codeInputsBizum">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
        </div>
        <div id="alertCodBizum" style="margin-bottom:12px"></div>
        <div class="d-flex gap-3">
            <button class="eco-btn eco-btn-outline flex-fill" onclick="cerrarModal('modalBizum')">Cancelar</button>
            <button class="eco-btn eco-btn-primary flex-fill" id="btnConfBizum" onclick="confirmarBizum()"><i class="bi bi-check2-circle"></i> Confirmar</button>
        </div>
    </div>
</div>

<!-- ══════ MODAL CÓDIGO TRANSFERENCIA ══════ -->
<div class="modal-overlay" id="modalTrans">
    <div class="modal-box">
        <div class="text-center mb-2" style="font-size:40px"><i class="bi bi-lock-fill"></i></div>
        <h5 class="text-center">Verificar transferencia</h5>
        <p class="text-center">Introduce el código de 5 dígitos enviado a tu correo para confirmar la transferencia.</p>
        <div class="code-inputs" id="codeInputsTrans">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
            <input class="code-input" maxlength="1" type="text" inputmode="numeric" pattern="[0-9]">
        </div>
        <div id="alertCodTrans" style="margin-bottom:12px"></div>
        <div class="d-flex gap-3">
            <button class="eco-btn eco-btn-outline flex-fill" onclick="cerrarModal('modalTrans')">Cancelar</button>
            <button class="eco-btn eco-btn-primary flex-fill" id="btnConfTrans" onclick="confirmarTrans()"><i class="bi bi-check2-circle"></i> Confirmar</button>
        </div>
    </div>
</div>

<!-- ══════ SCRIPTS ══════ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="./views/js/dashboard.js"></script>
</body>
</html>