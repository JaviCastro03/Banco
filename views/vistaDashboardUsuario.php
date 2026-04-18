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
<style>
:root {
  --eco-dark:    #1a3a2a;
  --eco-primary: #2d6a4f;
  --eco-mid:     #40916c;
  --eco-light:   #74c69d;
  --eco-pale:    #d8f3dc;
  --eco-cream:   #f6f4ef;
  --eco-white:   #ffffff;
  --eco-text:    #1c2b22;
  --eco-muted:   #6b7c72;
  --eco-border:  #e4ede7;
  --eco-card-shadow: 0 2px 16px rgba(26,58,42,.07);
  --eco-card-shadow-hover: 0 8px 32px rgba(26,58,42,.13);
  --sidebar-w: 260px;
  --radius: 16px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; background: var(--eco-cream); color: var(--eco-text); min-height: 100vh; display: flex; }

/* ── SIDEBAR ── */
.sidebar {
  width: var(--sidebar-w); min-height: 100vh; background: var(--eco-dark);
  display: flex; flex-direction: column; position: fixed; left: 0; top: 0; z-index: 100;
  transition: transform .3s ease;
}
.sidebar-logo {
  padding: 28px 24px 20px;
  display: flex; align-items: center; gap: 12px;
  border-bottom: 1px solid rgba(255,255,255,.08);
}
.sidebar-logo .logo-icon {
  width: 40px; height: 40px; background: var(--eco-mid);
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}
.sidebar-logo .logo-text { font-family: 'Playfair Display', serif; color: #fff; font-size: 20px; }
.sidebar-logo .logo-sub  { color: var(--eco-light); font-size: 10px; letter-spacing: 2px; text-transform: uppercase; margin-top: 1px; }

.sidebar-nav { flex: 1; padding: 16px 12px; }
.nav-section-label { color: rgba(255,255,255,.35); font-size: 10px; letter-spacing: 2px; text-transform: uppercase; padding: 16px 12px 8px; }
.nav-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 14px; border-radius: 10px; cursor: pointer;
  color: rgba(255,255,255,.65); font-size: 14px; font-weight: 500;
  transition: all .2s; margin-bottom: 2px; user-select: none;
}
.nav-item i { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }
.nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
.nav-item.active { background: var(--eco-primary); color: #fff; box-shadow: 0 4px 12px rgba(45,106,79,.4); }

.sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,.08); }
.sidebar-user {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px; border-radius: 10px; cursor: pointer;
  transition: background .2s;
}
.sidebar-user:hover { background: rgba(255,255,255,.07); }
.user-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, var(--eco-mid), var(--eco-light));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-weight: 600; font-size: 14px; flex-shrink: 0;
}
.user-name  { color: #fff; font-size: 13px; font-weight: 600; line-height: 1.2; }
.user-role  { color: rgba(255,255,255,.45); font-size: 11px; }
.btn-logout {
  display: flex; align-items: center; gap: 8px; padding: 10px 14px;
  color: rgba(255,255,255,.5); font-size: 13px; border-radius: 8px;
  cursor: pointer; transition: all .2s; margin-top: 4px; text-decoration: none;
}
.btn-logout:hover { background: rgba(220,53,69,.15); color: #ff6b6b; }

/* ── MAIN CONTENT ── */
.main-content { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; display: flex; flex-direction: column; }

.topbar {
  height: 68px; background: var(--eco-white); border-bottom: 1px solid var(--eco-border);
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 32px; position: sticky; top: 0; z-index: 50;
  box-shadow: 0 1px 8px rgba(0,0,0,.04);
}
.topbar-title { font-size: 18px; font-weight: 600; }
.topbar-title span { color: var(--eco-primary); }
.topbar-actions { display: flex; align-items: center; gap: 12px; }

.btn-notif {
  width: 40px; height: 40px; border-radius: 10px; border: none;
  background: var(--eco-cream); cursor: pointer; position: relative;
  display: flex; align-items: center; justify-content: center; font-size: 17px; color: var(--eco-text);
  transition: background .2s;
}
.btn-notif:hover { background: var(--eco-pale); }
.notif-badge {
  position: absolute; top: 5px; right: 5px; width: 16px; height: 16px;
  background: #e63946; border-radius: 50%; font-size: 9px; color: #fff;
  display: flex; align-items: center; justify-content: center; font-weight: 700; display: none;
}

.hamburger {
  display: none; background: none; border: none; cursor: pointer;
  font-size: 22px; color: var(--eco-text);
}

/* ── PAGE CONTENT ── */
.page-content { flex: 1; padding: 32px; }
.page-section { display: none; }
.page-section.active { display: block; animation: fadeIn .3s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

/* ── CARDS ── */
.eco-card {
  background: var(--eco-white); border-radius: var(--radius);
  border: 1px solid var(--eco-border);
  box-shadow: var(--eco-card-shadow);
  transition: box-shadow .2s;
}
.eco-card:hover { box-shadow: var(--eco-card-shadow-hover); }
.card-header-eco {
  padding: 20px 24px 0; display: flex; align-items: center;
  justify-content: space-between; margin-bottom: 16px;
}
.card-title-eco { font-size: 15px; font-weight: 600; color: var(--eco-text); }
.card-subtitle-eco { font-size: 12px; color: var(--eco-muted); margin-top: 2px; }

/* ── STAT CARDS ── */
.stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
.stat-card {
  background: var(--eco-white); border-radius: var(--radius);
  border: 1px solid var(--eco-border); padding: 22px 24px;
  box-shadow: var(--eco-card-shadow); transition: all .25s;
}
.stat-card:hover { box-shadow: var(--eco-card-shadow-hover); transform: translateY(-2px); }
.stat-label { font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--eco-muted); font-weight: 500; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 600; color: var(--eco-text); line-height: 1; margin-bottom: 6px; }
.stat-meta  { font-size: 12px; color: var(--eco-muted); }
.stat-meta .positive { color: var(--eco-primary); font-weight: 600; }
.stat-meta .negative { color: #e63946; font-weight: 600; }
.stat-card.primary-card { background: var(--eco-dark); border-color: var(--eco-dark); }
.stat-card.primary-card .stat-label { color: rgba(255,255,255,.55); }
.stat-card.primary-card .stat-value { color: #fff; }
.stat-card.primary-card .stat-meta  { color: rgba(255,255,255,.55); }
.stat-card.primary-card .stat-meta .positive { color: var(--eco-light); }

/* ── ACCIONES RÁPIDAS ── */
.quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-bottom: 24px; }
.quick-btn {
  display: flex; align-items: center; gap: 14px;
  background: var(--eco-white); border: 1px solid var(--eco-border);
  border-radius: var(--radius); padding: 18px 20px; cursor: pointer;
  transition: all .2s; text-align: left;
}
.quick-btn:hover { border-color: var(--eco-primary); background: var(--eco-pale); transform: translateY(-1px); }
.quick-btn.primary { background: var(--eco-primary); border-color: var(--eco-primary); }
.quick-btn.primary .quick-btn-title { color: #fff; }
.quick-btn.primary .quick-btn-sub   { color: rgba(255,255,255,.7); }
.quick-btn-icon {
  width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 19px;
  background: var(--eco-pale); color: var(--eco-primary);
}
.quick-btn.primary .quick-btn-icon { background: rgba(255,255,255,.15); color: #fff; }
.quick-btn-title { font-size: 14px; font-weight: 600; color: var(--eco-text); }
.quick-btn-sub   { font-size: 12px; color: var(--eco-muted); margin-top: 2px; }

/* ── MOVIMIENTOS ── */
.mov-list { list-style: none; }
.mov-item {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 20px; border-bottom: 1px solid var(--eco-border);
  transition: background .15s; cursor: default;
}
.mov-item:last-child { border-bottom: none; }
.mov-item:hover { background: var(--eco-cream); }
.mov-icon {
  width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.mov-icon.entrada { background: #d8f3dc; color: var(--eco-primary); }
.mov-icon.salida  { background: #fde8ea; color: #e63946; }
.mov-info { flex: 1; min-width: 0; }
.mov-name    { font-size: 14px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mov-date    { font-size: 12px; color: var(--eco-muted); margin-top: 2px; }
.mov-amount  { font-size: 15px; font-weight: 600; flex-shrink: 0; }
.mov-amount.positivo { color: var(--eco-primary); }
.mov-amount.negativo { color: #e63946; }
.mov-badge { font-size: 11px; padding: 2px 8px; border-radius: 20px; }
.mov-badge.completada { background: var(--eco-pale); color: var(--eco-primary); }
.mov-badge.pendiente  { background: #fff3cd; color: #856404; }
.mov-badge.rechazada  { background: #fde8ea; color: #e63946; }

/* ── FORMS ── */
.eco-form-group { margin-bottom: 18px; }
.eco-label { font-size: 12px; font-weight: 600; color: var(--eco-muted); letter-spacing: .5px; text-transform: uppercase; margin-bottom: 7px; display: block; }
.eco-input {
  width: 100%; padding: 12px 16px; border: 1.5px solid var(--eco-border);
  border-radius: 10px; font-size: 14px; font-family: inherit; background: var(--eco-white);
  color: var(--eco-text); transition: border-color .2s, box-shadow .2s; outline: none;
}
.eco-input:focus { border-color: var(--eco-primary); box-shadow: 0 0 0 3px rgba(45,106,79,.1); }
.eco-input::placeholder { color: #aab7b0; }

.eco-btn {
  display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
  border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
  border: none; font-family: inherit; transition: all .2s;
}
.eco-btn-primary { background: var(--eco-primary); color: #fff; }
.eco-btn-primary:hover { background: var(--eco-dark); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(45,106,79,.3); }
.eco-btn-outline { background: transparent; color: var(--eco-primary); border: 1.5px solid var(--eco-primary); }
.eco-btn-outline:hover { background: var(--eco-pale); }
.eco-btn-danger  { background: #e63946; color: #fff; }
.eco-btn-danger:hover { background: #c1121f; }
.eco-btn-sm { padding: 8px 16px; font-size: 13px; }
.eco-btn:disabled { opacity: .55; cursor: not-allowed; transform: none !important; }

/* ── ALERTS ── */
.eco-alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; margin-bottom: 18px; display: flex; align-items: flex-start; gap: 10px; }
.eco-alert-success { background: var(--eco-pale); color: var(--eco-dark); border: 1px solid var(--eco-light); }
.eco-alert-error   { background: #fde8ea; color: #7f1d1d; border: 1px solid #f5a3a9; }
.eco-alert-info    { background: #e0f2fe; color: #0c4a6e; border: 1px solid #7dd3fc; }
.eco-alert i       { margin-top: 1px; flex-shrink: 0; }

/* ── TARJETA BANCARIA ── */
.bank-card {
  background: linear-gradient(135deg, var(--eco-dark) 0%, var(--eco-primary) 60%, var(--eco-mid) 100%);
  border-radius: 20px; padding: 28px 28px 22px; color: #fff;
  position: relative; overflow: hidden; max-width: 380px;
  box-shadow: 0 20px 40px rgba(26,58,42,.3);
}
.bank-card::before {
  content:''; position: absolute; top:-60px; right:-60px;
  width: 220px; height: 220px; background: rgba(255,255,255,.05);
  border-radius: 50%;
}
.bank-card::after {
  content:''; position: absolute; bottom:-40px; left:-30px;
  width: 180px; height: 180px; background: rgba(255,255,255,.04);
  border-radius: 50%;
}
.card-chip { width: 44px; height: 32px; background: linear-gradient(135deg,#d4af37,#f9e08b); border-radius: 6px; margin-bottom: 24px; }
.card-number { font-size: 18px; letter-spacing: 4px; font-weight: 500; margin-bottom: 20px; font-family: 'Courier New', monospace; }
.card-row  { display: flex; justify-content: space-between; align-items: flex-end; }
.card-label-sm { font-size: 10px; opacity: .6; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 3px; }
.card-val   { font-size: 15px; font-weight: 600; }
.card-brand { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
.card-type-badge { position: absolute; top: 20px; right: 24px; background: rgba(255,255,255,.15); padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
.card-empty { border: 2px dashed var(--eco-border); border-radius: 20px; padding: 48px 28px; text-align: center; max-width: 380px; }
.card-empty-icon { font-size: 48px; color: var(--eco-light); margin-bottom: 16px; }
.card-empty h5 { font-size: 16px; font-weight: 600; color: var(--eco-text); margin-bottom: 8px; }
.card-empty p  { font-size: 13px; color: var(--eco-muted); margin-bottom: 20px; }

/* ── HISTORIAL TABLE ── */
.hist-table { width: 100%; border-collapse: collapse; }
.hist-table th { font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: var(--eco-muted); font-weight: 600; padding: 12px 16px; border-bottom: 2px solid var(--eco-border); text-align: left; }
.hist-table td { padding: 14px 16px; border-bottom: 1px solid var(--eco-border); font-size: 13px; vertical-align: middle; }
.hist-table tr:last-child td { border-bottom: none; }
.hist-table tr:hover td { background: var(--eco-cream); }
.tipo-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.tipo-bizum { background: #e0f2fe; color: #0369a1; }
.tipo-trans { background: #f3e8ff; color: #7e22ce; }

/* ── GRÁFICA ── */
.chart-container { padding: 24px; position: relative; height: 300px; }

/* ── PERFIL ── */
.perfil-avatar-lg {
  width: 80px; height: 80px; border-radius: 50%;
  background: linear-gradient(135deg, var(--eco-primary), var(--eco-light));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 30px; font-weight: 700; flex-shrink: 0;
}
.iban-box {
  background: var(--eco-cream); border: 1px solid var(--eco-border);
  border-radius: 10px; padding: 12px 16px;
  font-family: 'Courier New', monospace; font-size: 14px; letter-spacing: 1px;
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.btn-copy { background: none; border: none; cursor: pointer; color: var(--eco-primary); font-size: 16px; }
.btn-copy:hover { color: var(--eco-dark); }

/* ── NOTIFICACIONES PANEL ── */
.notif-panel {
  position: fixed; top: 68px; right: 16px; width: 360px;
  background: var(--eco-white); border-radius: var(--radius);
  border: 1px solid var(--eco-border); box-shadow: 0 16px 48px rgba(0,0,0,.12);
  z-index: 200; display: none; max-height: 480px; overflow-y: auto;
}
.notif-panel.open { display: block; animation: fadeIn .2s ease; }
.notif-header { padding: 16px 20px; border-bottom: 1px solid var(--eco-border); display: flex; align-items: center; justify-content: space-between; }
.notif-header h6 { font-size: 14px; font-weight: 600; margin: 0; }
.notif-item { padding: 14px 20px; border-bottom: 1px solid var(--eco-border); display: flex; gap: 12px; transition: background .15s; }
.notif-item:hover { background: var(--eco-cream); }
.notif-item.no-leida { background: #f0faf4; }
.notif-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--eco-primary); flex-shrink: 0; margin-top: 5px; }
.notif-dot.leida { background: transparent; }
.notif-text { font-size: 13px; line-height: 1.4; }
.notif-time { font-size: 11px; color: var(--eco-muted); margin-top: 3px; }
.notif-empty { text-align: center; padding: 32px; color: var(--eco-muted); font-size: 13px; }

/* ── MODAL CÓDIGO ── */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,.45);
  display: flex; align-items: center; justify-content: center; z-index: 300;
  display: none;
}
.modal-overlay.open { display: flex; animation: fadeIn .2s; }
.modal-box {
  background: var(--eco-white); border-radius: 20px; padding: 36px 32px;
  max-width: 420px; width: 100%; margin: 16px;
  box-shadow: 0 24px 64px rgba(0,0,0,.2);
}
.modal-box h5 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.modal-box p  { font-size: 13px; color: var(--eco-muted); margin-bottom: 24px; }
.code-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 24px; }
.code-input {
  width: 52px; height: 60px; border: 2px solid var(--eco-border); border-radius: 12px;
  text-align: center; font-size: 24px; font-weight: 700; color: var(--eco-primary);
  font-family: 'Courier New', monospace; outline: none; transition: border-color .2s;
}
.code-input:focus { border-color: var(--eco-primary); box-shadow: 0 0 0 3px rgba(45,106,79,.15); }

/* ── SPINNER ── */
.spinner-eco { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── SECTION HEADERS ── */
.section-header { margin-bottom: 28px; }
.section-header h2 { font-size: 24px; font-weight: 700; color: var(--eco-text); margin-bottom: 4px; }
.section-header p  { font-size: 14px; color: var(--eco-muted); }

/* ── SCROLL ── */
::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: var(--eco-border); border-radius: 3px; } ::-webkit-scrollbar-thumb:hover { background: var(--eco-light); }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.open { transform: translateX(0); }
  .main-content { margin-left: 0; }
  .hamburger { display: block; }
  .stat-grid { grid-template-columns: 1fr; }
  .quick-grid { grid-template-columns: 1fr; }
  .page-content { padding: 20px 16px; }
  .hist-table th:nth-child(4), .hist-table td:nth-child(4),
  .hist-table th:nth-child(5), .hist-table td:nth-child(5) { display: none; }
}
</style>
</head>
<body>

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🌿</div>
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
    <section class="page-section active" id="sec-inicio">
      <div class="section-header">
        <h2>Bienvenido, <span style="color:var(--eco-primary)" id="welcomeName"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span> 👋</h2>
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
              <label class="eco-label">📱 Teléfono del destinatario</label>
              <input type="tel" class="eco-input" id="bizumTel" placeholder="Ej: 612 345 678" maxlength="15">
            </div>
            <div class="eco-form-group">
              <label class="eco-label">💶 Importe (€)</label>
              <input type="number" class="eco-input" id="bizumImporte" placeholder="0,00" min="0.01" step="0.01" max="1000">
            </div>
            <div class="eco-form-group">
              <label class="eco-label">📝 Concepto (opcional)</label>
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
              <label class="eco-label">🏦 IBAN de destino</label>
              <input type="text" class="eco-input" id="transIBAN" placeholder="ES00 0000 0000 0000 0000 0000" maxlength="34" style="letter-spacing:1px;text-transform:uppercase">
            </div>
            <div class="eco-form-group">
              <label class="eco-label">👤 Titular de la cuenta</label>
              <input type="text" class="eco-input" id="transTitular" placeholder="Nombre del beneficiario">
            </div>
            <div class="eco-form-group">
              <label class="eco-label">💶 Importe (€)</label>
              <input type="number" class="eco-input" id="transImporte" placeholder="0,00" min="0.01" step="0.01">
            </div>
            <div class="eco-form-group">
              <label class="eco-label">📝 Concepto</label>
              <input type="text" class="eco-input" id="transConcepto" placeholder="Ej: Alquiler mayo" maxlength="255">
            </div>
            <button class="eco-btn eco-btn-primary w-100" id="btnEnviarTrans" onclick="solicitarCodigoTrans()">
              <i class="bi bi-arrow-right-circle-fill"></i> Realizar transferencia
            </button>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="eco-card p-4">
            <div class="text-center mb-3"><i class="bi bi-bank fs-2" style="color:var(--eco-primary)"></i></div>
            <h6 class="fw-bold mb-2">Información importante</h6>
            <p style="font-size:13px;color:var(--eco-muted);line-height:1.6">
              Las transferencias SEPA a otras entidades pueden tardar <strong>1-2 días hábiles</strong>. Para cuentas internas de EcoBank el saldo se actualiza al instante.<br><br>
              Para importes superiores a <strong>€500</strong> recibirás un código de verificación adicional en tu email.<br><br>
              <span style="color:#e63946">⚠️ Verifica siempre el IBAN antes de confirmar.</span>
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ════════ HISTORIAL ════════ -->
    <section class="page-section" id="sec-historial">
      <div class="section-header d-flex justify-content-between align-items-start">
        <div>
          <h2>Historial de <span style="color:var(--eco-primary)">Movimientos</span></h2>
          <p>Todos tus ingresos y gastos en un solo lugar.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <select class="eco-input" style="width:auto;font-size:13px;padding:9px 14px" id="filtroTipo" onchange="filtrarHistorial()">
            <option value="">Todos los tipos</option>
            <option value="bizum">Bizum</option>
            <option value="transferencia">Transferencia</option>
          </select>
          <select class="eco-input" style="width:auto;font-size:13px;padding:9px 14px" id="filtroDir" onchange="filtrarHistorial()">
            <option value="">Todas las direcciones</option>
            <option value="entrada">Entradas</option>
            <option value="salida">Salidas</option>
          </select>
        </div>
      </div>
      <div class="eco-card">
        <div style="overflow-x:auto">
          <table class="hist-table" id="histTable">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Concepto / Destinatario</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Importe</th>
              </tr>
            </thead>
            <tbody id="histBody">
              <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--eco-muted)"><div class="spinner-border spinner-border-sm"></div> Cargando...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ════════ GRÁFICA ════════ -->
    <section class="page-section" id="sec-grafica">
      <div class="section-header">
        <h2>Análisis <span style="color:var(--eco-primary)">Financiero</span></h2>
        <p>Visualiza tus ingresos y gastos de los últimos 6 meses.</p>
      </div>
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="eco-card">
            <div class="card-header-eco">
              <div>
                <div class="card-title-eco">Ingresos vs Gastos</div>
                <div class="card-subtitle-eco">Últimos 6 meses</div>
              </div>
            </div>
            <div class="chart-container">
              <canvas id="myChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="eco-card p-4 mb-4">
            <div class="card-title-eco mb-3">Resumen mensual</div>
            <div class="d-flex align-items-center gap-3 mb-3">
              <div style="width:40px;height:40px;border-radius:10px;background:var(--eco-pale);display:flex;align-items:center;justify-content:center;font-size:18px">📈</div>
              <div>
                <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px">Recibido</div>
                <div style="font-size:20px;font-weight:700;color:var(--eco-primary)" id="grafRecibido">—</div>
              </div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="width:40px;height:40px;border-radius:10px;background:#fde8ea;display:flex;align-items:center;justify-content:center;font-size:18px">📉</div>
              <div>
                <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px">Enviado</div>
                <div style="font-size:20px;font-weight:700;color:#e63946" id="grafEnviado">—</div>
              </div>
            </div>
          </div>
          <div class="eco-card p-4">
            <div class="card-title-eco mb-2">Leyenda</div>
            <div class="d-flex align-items-center gap-2 mb-2"><div style="width:14px;height:14px;border-radius:3px;background:#2d6a4f"></div><span style="font-size:13px">Recibido</span></div>
            <div class="d-flex align-items-center gap-2"><div style="width:14px;height:14px;border-radius:3px;background:#e63946"></div><span style="font-size:13px">Gastado</span></div>
          </div>
        </div>
      </div>
    </section>

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
              <div class="card-empty-icon"><i class="bi bi-credit-card"></i></div>
              <h5>Sin tarjeta activa</h5>
              <p>No tienes ninguna tarjeta asociada a tu cuenta.</p>
              <button class="eco-btn eco-btn-primary" onclick="crearTarjeta()"><i class="bi bi-plus-circle-fill"></i> Solicitar tarjeta</button>
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
                <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">El número completo de tu tarjeta está cifrado. Solo se muestra parcialmente.</p>
              </div>
            </div>
            <div class="d-flex gap-3 mb-3 align-items-start">
              <i class="bi bi-eye-slash-fill fs-5" style="color:var(--eco-primary);margin-top:2px"></i>
              <div>
                <strong style="font-size:13px">CVV oculto</strong>
                <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">El código de seguridad nunca se muestra por pantalla.</p>
              </div>
            </div>
            <div class="d-flex gap-3 align-items-start">
              <i class="bi bi-calendar-check-fill fs-5" style="color:var(--eco-primary);margin-top:2px"></i>
              <div>
                <strong style="font-size:13px">Renovación automática</strong>
                <p style="font-size:12px;color:var(--eco-muted);margin:3px 0 0">Tu tarjeta se renueva automáticamente antes de su vencimiento.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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
                  <span style="background:var(--eco-pale);color:var(--eco-primary);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">Cliente verificado ✓</span>
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
            <h6 class="fw-bold mb-3">🔐 Cambiar contraseña</h6>
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
            <h6 class="fw-bold mb-3">🏦 Datos bancarios</h6>
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
            <h6 class="fw-bold mb-3">ℹ️ Información de cuenta</h6>
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
              <span style="font-size:13px;font-weight:600;color:var(--eco-primary)">✓ Activo</span>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main><!-- /page-content -->
</div><!-- /main-content -->

<!-- ══════ MODAL CÓDIGO BIZUM ══════ -->
<div class="modal-overlay" id="modalBizum">
  <div class="modal-box">
    <div class="text-center mb-2" style="font-size:40px">📧</div>
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
    <div class="text-center mb-2" style="font-size:40px">🔐</div>
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
<script>
/* ═══════════════════════════════════════════
   NAVIGATION
═══════════════════════════════════════════ */
let chartInstance = null;
let historialData = [];
const CONTROLLER = 'index.php';

function showSection(name, el) {
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('sec-' + name).classList.add('active');
  if (el) el.classList.add('active');
  else {
    const found = document.querySelector('[data-section="'+name+'"]');
    if (found) found.classList.add('active');
  }
  const titles = {inicio:'Inicio <span>/ Dashboard</span>',bizum:'Bizum <span>/ Enviar</span>',transferencia:'Transferencia <span>/ Nueva</span>',historial:'Historial <span>/ Movimientos</span>',grafica:'Análisis <span>/ Gráfica</span>',tarjeta:'Tarjeta <span>/ Mi tarjeta</span>',perfil:'Perfil <span>/ Mi cuenta</span>'};
  document.getElementById('topbarTitle').innerHTML = titles[name] || name;
  if (name === 'inicio')   loadDashboard();
  if (name === 'historial') loadHistorial();
  if (name === 'grafica')   loadGrafica();
  if (name === 'tarjeta')   loadTarjeta();
  if (name === 'perfil')    loadPerfil();
  // cerrar sidebar en móvil
  document.getElementById('sidebar').classList.remove('open');
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

/* ═══════════════════════════════════════════
   AJAX helper
═══════════════════════════════════════════ */
function ajax(data, cb) {
  data.ajax = 1;
  fetch(CONTROLLER, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: Object.entries(data).map(([k,v])=>encodeURIComponent(k)+'='+encodeURIComponent(v)).join('&')
  }).then(r=>r.json()).then(cb).catch(e=>console.error(e));
}

/* ═══════════════════════════════════════════
   ALERTS
═══════════════════════════════════════════ */
function showAlert(id, msg, type='success') {
  const icons = {success:'check-circle-fill', error:'exclamation-triangle-fill', info:'info-circle-fill'};
  document.getElementById(id).innerHTML = `<div class="eco-alert eco-alert-${type}"><i class="bi bi-${icons[type]}"></i><span>${msg}</span></div>`;
  if (type==='success') setTimeout(()=>{const el=document.getElementById(id);if(el)el.innerHTML='';},4000);
}

/* ═══════════════════════════════════════════
   FORMATO
═══════════════════════════════════════════ */
function formatEur(n) {
  return '€ ' + parseFloat(n||0).toLocaleString('es-ES',{minimumFractionDigits:2,maximumFractionDigits:2});
}
function formatFecha(f) {
  if(!f) return '—';
  const d=new Date(f);
  const hoy=new Date(); const ayer=new Date(hoy); ayer.setDate(ayer.getDate()-1);
  if(d.toDateString()===hoy.toDateString()) return 'Hoy · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
  if(d.toDateString()===ayer.toDateString()) return 'Ayer · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
  return d.toLocaleDateString('es-ES',{day:'2-digit',month:'short'})+' · '+d.toLocaleTimeString('es-ES',{hour:'2-digit',minute:'2-digit'});
}
function iniAvatar(nombre) {
  return (nombre||'U').trim().charAt(0).toUpperCase();
}

/* ═══════════════════════════════════════════
   DASHBOARD
═══════════════════════════════════════════ */
function loadDashboard() {
  ajax({accion:'getDashboard'}, r=>{
    if(!r.ok) return;
    const d=r.datos, res=r.resumen, mov=r.movimientos;
    const nombre = (d.nombre||'').trim();
    const av = iniAvatar(nombre);
    document.getElementById('statSaldo').textContent   = formatEur(d.saldo);
    document.getElementById('statIban').textContent    = d.iban ? d.iban.replace(/(.{4})/g,'$1 ').trim() : '—';
    document.getElementById('statEnviado').textContent  = formatEur(res.enviado);
    document.getElementById('statRecibido').textContent = formatEur(res.recibido);
    document.getElementById('statNumEnv').textContent   = res.num_enviadas || 0;
    document.getElementById('sidebarName').textContent  = nombre;
    document.getElementById('sidebarAvatar').textContent= av;
    document.getElementById('topAvatar').textContent    = av;
    document.getElementById('welcomeName').textContent  = nombre;
    renderMovList('lastMovList', mov);
  });
}

function renderMovList(id, movs) {
  const ul = document.getElementById(id);
  if(!movs||!movs.length){ul.innerHTML='<li class="mov-item"><div style="color:var(--eco-muted);font-size:13px">Sin movimientos recientes.</div></li>';return;}
  ul.innerHTML = movs.map(m=>{
    const ent = m.direccion==='entrada';
    const icon = m.tipo==='bizum' ? '⚡' : '🏦';
    const titulo = m.destinatario || m.concepto || (m.tipo==='bizum'?'Bizum':'Transferencia');
    return `<li class="mov-item">
      <div class="mov-icon ${m.direccion}">${icon}</div>
      <div class="mov-info">
        <div class="mov-name">${htmlE(titulo)}</div>
        <div class="mov-date">${formatFecha(m.fecha)}</div>
      </div>
      <div class="mov-amount ${ent?'positivo':'negativo'}">${ent?'+':'-'}${formatEur(m.importe)}</div>
    </li>`;
  }).join('');
}

/* ═══════════════════════════════════════════
   HISTORIAL
═══════════════════════════════════════════ */
function loadHistorial() {
  ajax({accion:'getHistorial'}, r=>{
    if(!r.ok) return;
    historialData = r.historial || [];
    renderHistorial(historialData);
  });
}

function renderHistorial(data) {
  const tbody = document.getElementById('histBody');
  if(!data.length){tbody.innerHTML='<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--eco-muted)">Sin movimientos registrados.</td></tr>';return;}
  tbody.innerHTML = data.map(m=>{
    const ent = m.direccion==='entrada';
    const tipoBadge = m.tipo==='bizum' ? '<span class="tipo-badge tipo-bizum">⚡ Bizum</span>' : '<span class="tipo-badge tipo-trans">🏦 Transferencia</span>';
    const estadoBadge = `<span class="mov-badge ${m.estado}">${m.estado}</span>`;
    const titulo = m.destinatario || m.concepto || '—';
    return `<tr>
      <td>${tipoBadge}</td>
      <td><div style="font-weight:500;font-size:13px">${htmlE(titulo)}</div><div style="font-size:11px;color:var(--eco-muted)">${htmlE(m.concepto||'')}</div></td>
      <td style="font-size:12px;white-space:nowrap">${formatFecha(m.fecha)}</td>
      <td>${estadoBadge}</td>
      <td style="font-weight:600;${ent?'color:var(--eco-primary)':'color:#e63946'}">${ent?'+':'-'}${formatEur(m.importe)}</td>
    </tr>`;
  }).join('');
}

function filtrarHistorial() {
  const tipo = document.getElementById('filtroTipo').value;
  const dir  = document.getElementById('filtroDir').value;
  let data = historialData;
  if(tipo) data = data.filter(m=>m.tipo===tipo);
  if(dir)  data = data.filter(m=>m.direccion===dir);
  renderHistorial(data);
}

/* ═══════════════════════════════════════════
   GRÁFICA
═══════════════════════════════════════════ */
function loadGrafica() {
  ajax({accion:'getGrafica'}, r=>{
    if(!r.ok) return;
    const data = r.datos || [];
    const labels  = data.map(d=>{ const p=d.mes.split('-'); return ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'][parseInt(p[1])-1]+' '+p[0].slice(2); });
    const gastado = data.map(d=>parseFloat(d.gastado)||0);
    const recibido= data.map(d=>parseFloat(d.recibido)||0);

    const totalG = gastado.reduce((a,b)=>a+b,0);
    const totalR = recibido.reduce((a,b)=>a+b,0);
    document.getElementById('grafEnviado').textContent  = formatEur(totalG);
    document.getElementById('grafRecibido').textContent = formatEur(totalR);

    if(chartInstance) chartInstance.destroy();
    const ctx = document.getElementById('myChart').getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label:'Recibido', data: recibido, backgroundColor:'rgba(45,106,79,.75)', borderRadius:8, borderSkipped:false },
          { label:'Gastado',  data: gastado,  backgroundColor:'rgba(230,57,70,.7)',  borderRadius:8, borderSkipped:false }
        ]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label: ctx=>' '+formatEur(ctx.raw) } } },
        scales:{
          x:{ grid:{ display:false }, ticks:{ font:{ family:"'DM Sans',sans-serif", size:12 } } },
          y:{ grid:{ color:'rgba(0,0,0,.05)' }, ticks:{ callback:v=>formatEur(v), font:{ size:11 } } }
        }
      }
    });
  });
}

/* ═══════════════════════════════════════════
   TARJETA
═══════════════════════════════════════════ */
function loadTarjeta() {
  ajax({accion:'getTarjeta'}, r=>{
    if(!r.ok) return;
    if(r.tarjeta) renderTarjeta(r.tarjeta);
  });
}

function renderTarjeta(t) {
  const num = t.numero_tarjeta;
  const masked = '**** **** **** ' + num.slice(-4);
  const exp = t.fecha_caducidad ? t.fecha_caducidad.slice(0,7).replace('-','/').split('/').reverse().join('/') : '??/??';
  document.getElementById('cardDisplay').innerHTML = `
    <div class="bank-card">
      <div class="card-type-badge">${t.tipo||'débito'}</div>
      <div class="card-chip"></div>
      <div class="card-number">${masked}</div>
      <div class="card-row">
        <div>
          <div class="card-label-sm">Titular</div>
          <div class="card-val">${htmlE(t.titular||'')}</div>
        </div>
        <div>
          <div class="card-label-sm">Válida hasta</div>
          <div class="card-val">${exp}</div>
        </div>
        <div class="card-brand">VISA</div>
      </div>
    </div>
    <div class="mt-3 d-flex gap-2">
      <div class="eco-card p-3 flex-fill text-center">
        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">CVV</div>
        <div style="font-size:20px;font-weight:700;letter-spacing:4px">•••</div>
      </div>
      <div class="eco-card p-3 flex-fill text-center">
        <div style="font-size:11px;color:var(--eco-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Estado</div>
        <div style="font-size:13px;font-weight:600;color:var(--eco-primary)">✓ Activa</div>
      </div>
    </div>`;
}

function crearTarjeta() {
  showAlert('alertTarjeta','Creando tarjeta...','info');
  ajax({accion:'crearTarjeta'}, r=>{
    showAlert('alertTarjeta', r.mensaje, r.ok?'success':'error');
    if(r.ok && r.tarjeta) renderTarjeta(r.tarjeta);
  });
}

/* ═══════════════════════════════════════════
   PERFIL
═══════════════════════════════════════════ */
function loadPerfil() {
  ajax({accion:'getDashboard'}, r=>{
    if(!r.ok) return;
    const d=r.datos;
    const nombre=(d.nombre||'').trim(), ape=(d.apellidos||'').trim();
    document.getElementById('perfilAvatar').textContent  = iniAvatar(nombre);
    document.getElementById('perfilNombreH').textContent = nombre+' '+ape;
    document.getElementById('perfilEmailH').textContent  = d.email||'';
    document.getElementById('perfilIBAN').textContent    = d.iban||'—';
    document.getElementById('perfilTipoCuenta').textContent = d.tipo_cuenta||'corriente';
    document.getElementById('perfilDNI').textContent     = d.dni||'—';
    document.getElementById('perfilDesde').textContent   = d.created_at ? new Date(d.created_at).toLocaleDateString('es-ES',{year:'numeric',month:'long'}) : '—';
    document.getElementById('pNombre').value    = nombre;
    document.getElementById('pApellidos').value = ape;
    document.getElementById('pEmail').value     = d.email||'';
    document.getElementById('pTelefono').value  = (d.telefono||'').trim();
    document.getElementById('pProvincia').value = d.provincia||'';
    document.getElementById('pMunicipio').value = d.municipio||'';
    document.getElementById('pVia').value       = d.via||'';
    document.getElementById('pNumPiso').value   = d.numero_piso||'';
  });
}

function guardarPerfil() {
  ajax({
    accion:'actualizarPerfil',
    nombre:     document.getElementById('pNombre').value,
    apellidos:  document.getElementById('pApellidos').value,
    email:      document.getElementById('pEmail').value,
    telefono:   document.getElementById('pTelefono').value,
    provincia:  document.getElementById('pProvincia').value,
    municipio:  document.getElementById('pMunicipio').value,
    via:        document.getElementById('pVia').value,
    numero_piso:document.getElementById('pNumPiso').value
  }, r => showAlert('alertPerfil', r.mensaje, r.ok?'success':'error'));
}

function cambiarPass() {
  const act=document.getElementById('passActual').value;
  const nva=document.getElementById('passNueva').value;
  const rep=document.getElementById('passRepeat').value;
  if(nva!==rep){showAlert('alertPass','Las contraseñas nuevas no coinciden.','error');return;}
  if(nva.length<6){showAlert('alertPass','La nueva contraseña debe tener al menos 6 caracteres.','error');return;}
  ajax({accion:'cambiarPassword',password_actual:act,nueva_password:nva}, r=>{
    showAlert('alertPass',r.mensaje,r.ok?'success':'error');
    if(r.ok){document.getElementById('passActual').value='';document.getElementById('passNueva').value='';document.getElementById('passRepeat').value='';}
  });
}

function copiarIban() {
  const iban = document.getElementById('perfilIBAN').textContent;
  navigator.clipboard.writeText(iban).then(()=>showAlert('alertPerfil','IBAN copiado al portapapeles.','success'));
}

/* ═══════════════════════════════════════════
   BIZUM
═══════════════════════════════════════════ */
function solicitarCodigoBizum() {
  const tel  = document.getElementById('bizumTel').value.trim();
  const imp  = document.getElementById('bizumImporte').value;
  const conc = document.getElementById('bizumConcepto').value.trim();
  if(!tel||!imp||parseFloat(imp)<=0){showAlert('alertBizum','Rellena el teléfono y el importe correctamente.','error');return;}
  const btn=document.getElementById('btnEnviarBizum');
  btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span> Enviando...';
  ajax({accion:'solicitarCodigoBizum',telefono:tel,importe:imp,concepto:conc||'Bizum'}, r=>{
    btn.disabled=false; btn.innerHTML='<i class="bi bi-lightning-charge-fill"></i> Enviar Bizum';
    if(r.ok){showAlert('alertBizum',r.mensaje,'success'); abrirModal('modalBizum'); limpiarCodeInputs('codeInputsBizum');}
    else showAlert('alertBizum',r.mensaje,'error');
  });
}

function confirmarBizum() {
  const codigo = getCodeValue('codeInputsBizum');
  if(codigo.length!==5){showAlert('alertCodBizum','Introduce los 5 dígitos del código.','error');return;}
  const btn=document.getElementById('btnConfBizum');
  btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span>';
  ajax({accion:'confirmarBizum',codigo}, r=>{
    btn.disabled=false; btn.innerHTML='<i class="bi bi-check2-circle"></i> Confirmar';
    if(r.ok){
      cerrarModal('modalBizum');
      showAlert('alertBizum',r.mensaje,'success');
      document.getElementById('bizumTel').value='';
      document.getElementById('bizumImporte').value='';
      document.getElementById('bizumConcepto').value='';
      loadDashboard();
    } else showAlert('alertCodBizum',r.mensaje,'error');
  });
}

/* ═══════════════════════════════════════════
   TRANSFERENCIA
═══════════════════════════════════════════ */
function solicitarCodigoTrans() {
  const iban  = document.getElementById('transIBAN').value.trim().replace(/\s/g,'');
  const tit   = document.getElementById('transTitular').value.trim();
  const imp   = document.getElementById('transImporte').value;
  const conc  = document.getElementById('transConcepto').value.trim();
  if(!iban||!tit||!imp||parseFloat(imp)<=0){showAlert('alertTrans','Rellena todos los campos obligatorios.','error');return;}
  const btn=document.getElementById('btnEnviarTrans');
  btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span> Verificando...';
  ajax({accion:'solicitarCodigoTransferencia',iban_destino:iban,titular_destino:tit,importe:imp,concepto:conc||'Transferencia'}, r=>{
    btn.disabled=false; btn.innerHTML='<i class="bi bi-arrow-right-circle-fill"></i> Realizar transferencia';
    if(r.ok){showAlert('alertTrans',r.mensaje,'success'); abrirModal('modalTrans'); limpiarCodeInputs('codeInputsTrans');}
    else showAlert('alertTrans',r.mensaje,'error');
  });
}

function confirmarTrans() {
  const codigo = getCodeValue('codeInputsTrans');
  if(codigo.length!==5){showAlert('alertCodTrans','Introduce los 5 dígitos del código.','error');return;}
  const btn=document.getElementById('btnConfTrans');
  btn.disabled=true; btn.innerHTML='<span class="spinner-eco"></span>';
  ajax({accion:'confirmarTransferencia',codigo}, r=>{
    btn.disabled=false; btn.innerHTML='<i class="bi bi-check2-circle"></i> Confirmar';
    if(r.ok){
      cerrarModal('modalTrans');
      showAlert('alertTrans',r.mensaje,'success');
      document.getElementById('transIBAN').value='';
      document.getElementById('transTitular').value='';
      document.getElementById('transImporte').value='';
      document.getElementById('transConcepto').value='';
      loadDashboard();
    } else showAlert('alertCodTrans',r.mensaje,'error');
  });
}

/* ═══════════════════════════════════════════
   MODALES & CODE INPUTS
═══════════════════════════════════════════ */
function abrirModal(id) { document.getElementById(id).classList.add('open'); }
function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }

function getCodeValue(containerId) {
  return Array.from(document.querySelectorAll('#'+containerId+' .code-input')).map(i=>i.value).join('');
}
function limpiarCodeInputs(containerId) {
  document.querySelectorAll('#'+containerId+' .code-input').forEach(i=>i.value='');
}

// Auto-advance code inputs
document.querySelectorAll('.code-inputs').forEach(container=>{
  const inputs = container.querySelectorAll('.code-input');
  inputs.forEach((inp,i)=>{
    inp.addEventListener('input',e=>{
      inp.value = inp.value.replace(/[^0-9]/g,'').slice(-1);
      if(inp.value && i<inputs.length-1) inputs[i+1].focus();
    });
    inp.addEventListener('keydown',e=>{
      if(e.key==='Backspace'&&!inp.value&&i>0) inputs[i-1].focus();
    });
    inp.addEventListener('paste',e=>{
      e.preventDefault();
      const paste=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'');
      inputs.forEach((inp2,j)=>{ inp2.value=paste[j]||''; });
      inputs[Math.min(paste.length,inputs.length)-1].focus();
    });
  });
});

// Cerrar modal clickando fuera
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click',e=>{ if(e.target===m) m.classList.remove('open'); });
});

/* ═══════════════════════════════════════════
   NOTIFICACIONES
═══════════════════════════════════════════ */
function toggleNotif() {
  const p=document.getElementById('notifPanel');
  p.classList.toggle('open');
  if(p.classList.contains('open')) loadNotifs();
}

function loadNotifs() {
  ajax({accion:'getNotificaciones'}, r=>{
    if(!r.ok) return;
    const badge=document.getElementById('notifBadge');
    const n=r.no_leidas||0;
    badge.textContent=n; badge.style.display=n>0?'flex':'none';
    const list=document.getElementById('notifList');
    if(!r.notificaciones||!r.notificaciones.length){list.innerHTML='<div class="notif-empty"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>Sin notificaciones</div>';return;}
    list.innerHTML=r.notificaciones.map(n=>`
      <div class="notif-item ${n.leida?'':'no-leida'}">
        <div class="notif-dot ${n.leida?'leida':''}"></div>
        <div>
          <div class="notif-text">${htmlE(n.contenido)}</div>
          <div class="notif-time">${formatFecha(n.created_at)}</div>
        </div>
      </div>`).join('');
  });
}

function marcarLeidas() {
  ajax({accion:'marcarLeidas'}, ()=>loadNotifs());
}

// Cerrar notif panel clickando fuera
document.addEventListener('click', e=>{
  const panel=document.getElementById('notifPanel');
  const btn=document.getElementById('btnNotif');
  if(!panel.contains(e.target)&&!btn.contains(e.target)) panel.classList.remove('open');
});

/* ═══════════════════════════════════════════
   UTILS
═══════════════════════════════════════════ */
function htmlE(str) {
  if(!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══════════════════════════════════════════
   INIT
═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', ()=>{
  loadDashboard();
  // cargar badge notifs
  ajax({accion:'getNotificaciones'}, r=>{
    if(r.ok){ const n=r.no_leidas||0; const b=document.getElementById('notifBadge'); b.textContent=n; b.style.display=n>0?'flex':'none'; }
  });
});
</script>
</body>
</html>
