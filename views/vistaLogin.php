<?php if(session_status()===PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoBank — Acceso</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root {
  --eco-dark:#1a3a2a; --eco-primary:#2d6a4f; --eco-mid:#40916c;
  --eco-light:#74c69d; --eco-pale:#d8f3dc; --eco-cream:#f6f4ef;
  --eco-white:#fff; --eco-text:#1c2b22; --eco-muted:#6b7c72;
  --eco-border:#e4ede7;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;min-height:100vh;display:grid;grid-template-columns:1fr 1fr;background:var(--eco-cream)}

/* LEFT PANEL */
.left-panel{
  background:var(--eco-dark);
  display:flex;flex-direction:column;justify-content:space-between;
  padding:48px;position:relative;overflow:hidden;
}
.left-panel::before{
  content:'';position:absolute;top:-100px;right:-100px;
  width:400px;height:400px;border-radius:50%;
  background:rgba(116,198,157,.06);pointer-events:none;
}
.left-panel::after{
  content:'';position:absolute;bottom:-80px;left:-60px;
  width:300px;height:300px;border-radius:50%;
  background:rgba(255,255,255,.03);pointer-events:none;
}
.left-logo{display:flex;align-items:center;gap:14px}
.left-logo-icon{width:48px;height:48px;background:var(--eco-primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px}
.left-logo-text{font-family:'Playfair Display',serif;color:#fff;font-size:26px}
.left-logo-sub{color:rgba(255,255,255,.4);font-size:11px;letter-spacing:2px;text-transform:uppercase;margin-top:2px}

.left-hero{z-index:1}
.left-hero h1{font-family:'Playfair Display',serif;color:#fff;font-size:44px;line-height:1.15;margin-bottom:20px}
.left-hero h1 span{color:var(--eco-light)}
.left-hero p{color:rgba(255,255,255,.55);font-size:16px;line-height:1.7;max-width:340px}

.left-features{display:flex;flex-direction:column;gap:14px;z-index:1}
.feature-item{display:flex;align-items:center;gap:14px;padding:16px 18px;background:rgba(255,255,255,.06);border-radius:12px;border:1px solid rgba(255,255,255,.06)}
.feature-icon{width:36px;height:36px;border-radius:9px;background:rgba(116,198,157,.15);display:flex;align-items:center;justify-content:center;color:var(--eco-light);font-size:16px;flex-shrink:0}
.feature-text{color:rgba(255,255,255,.75);font-size:13px;font-weight:500}

/* RIGHT PANEL */
.right-panel{
  display:flex;align-items:center;justify-content:center;
  padding:48px 56px;
}
.form-wrapper{width:100%;max-width:380px}

.form-header{margin-bottom:36px}
.form-header h2{font-size:28px;font-weight:700;color:var(--eco-text);margin-bottom:6px}
.form-header p{font-size:14px;color:var(--eco-muted)}

.form-group{margin-bottom:18px}
.eco-label{display:block;font-size:12px;font-weight:600;color:var(--eco-muted);letter-spacing:.5px;text-transform:uppercase;margin-bottom:7px}
.input-wrap{position:relative}
.eco-input{
  width:100%;padding:13px 16px 13px 44px;
  border:1.5px solid var(--eco-border);border-radius:11px;
  font-size:14px;font-family:inherit;background:var(--eco-white);
  color:var(--eco-text);outline:none;transition:border-color .2s,box-shadow .2s;
}
.eco-input:focus{border-color:var(--eco-primary);box-shadow:0 0 0 3px rgba(45,106,79,.1)}
.eco-input::placeholder{color:#aab7b0}
.input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--eco-muted);font-size:16px;pointer-events:none}
.pass-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--eco-muted);cursor:pointer;font-size:16px;background:none;border:none}
.pass-toggle:hover{color:var(--eco-primary)}

.eco-btn-login{
  width:100%;padding:14px;border-radius:11px;background:var(--eco-primary);
  color:#fff;font-size:15px;font-weight:600;font-family:inherit;
  border:none;cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;
}
.eco-btn-login:hover{background:var(--eco-dark);box-shadow:0 6px 20px rgba(45,106,79,.3)}

.form-footer{text-align:center;margin-top:24px;font-size:13px;color:var(--eco-muted)}
.form-footer a{color:var(--eco-primary);font-weight:600;text-decoration:none}
.form-footer a:hover{text-decoration:underline}

.alert-box{
  padding:13px 16px;border-radius:10px;font-size:13px;
  margin-bottom:20px;display:flex;align-items:center;gap:10px;
  background:#fde8ea;color:#7f1d1d;border:1px solid #f5a3a9;
}
.alert-box.success{background:#d8f3dc;color:#1a3a2a;border-color:#74c69d}

.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--eco-muted);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--eco-border)}

@media(max-width:768px){
  body{grid-template-columns:1fr}
  .left-panel{display:none}
  .right-panel{padding:32px 24px}
}
</style>
</head>
<body>

<!-- LEFT -->
<div class="left-panel">
  <div class="left-logo">
    <div class="left-logo-icon">🌿</div>
    <div>
      <div class="left-logo-text">EcoBank</div>
      <div class="left-logo-sub">Banca Sostenible</div>
    </div>
  </div>

  <div class="left-hero">
    <h1>Tu dinero,<br><span>más inteligente</span><br>que nunca.</h1>
    <p>Gestiona tus finanzas con total seguridad desde cualquier lugar. Bizum, transferencias, historial y mucho más.</p>
  </div>

  <div class="left-features">
    <div class="feature-item"><div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div><div class="feature-text">Bizum al instante · sin comisiones</div></div>
    <div class="feature-item"><div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div><div class="feature-text">Verificación en dos pasos por email</div></div>
    <div class="feature-item"><div class="feature-icon"><i class="bi bi-bar-chart-line-fill"></i></div><div class="feature-text">Análisis visual de tus finanzas</div></div>
  </div>
</div>

<!-- RIGHT -->
<div class="right-panel">
  <div class="form-wrapper">
    <div class="form-header">
      <h2>Bienvenido de nuevo</h2>
      <p>Accede a tu cuenta EcoBank</p>
    </div>

    <?php if(isset($_SESSION["mensaje"])): ?>
    <div class="alert-box">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <?= htmlspecialchars($_SESSION["mensaje"]) ?>
    </div>
    <?php unset($_SESSION["mensaje"]); endif; ?>

    <form method="POST" action="index.php">
      <div class="form-group">
        <label class="eco-label">Email</label>
        <div class="input-wrap">
          <i class="bi bi-envelope-fill input-icon"></i>
          <input type="email" name="email" class="eco-input" placeholder="tu@email.com" required autocomplete="email">
        </div>
      </div>
      <div class="form-group">
        <label class="eco-label">Contraseña</label>
        <div class="input-wrap">
          <i class="bi bi-lock-fill input-icon"></i>
          <input type="password" name="password" id="passField" class="eco-input" placeholder="••••••••" required autocomplete="current-password">
          <button type="button" class="pass-toggle" onclick="togglePass()"><i class="bi bi-eye" id="eyeIcon"></i></button>
        </div>
      </div>
      <button type="submit" name="iniciarSesion" class="eco-btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
      </button>
    </form>

    <div class="divider">o</div>

    <div class="form-footer">
      ¿No tienes cuenta?
      <form method="POST" action="index.php" style="display:inline">
        <button type="submit" name="registro" style="background:none;border:none;padding:0;cursor:pointer;color:var(--eco-primary);font-weight:600;font-family:inherit;font-size:13px">Regístrate gratis</button>
      </form>
    </div>
  </div>
</div>

<script>
function togglePass() {
  const f=document.getElementById('passField'), i=document.getElementById('eyeIcon');
  if(f.type==='password'){f.type='text'; i.className='bi bi-eye-slash';}
  else{f.type='password'; i.className='bi bi-eye';}
}
</script>
</body>
</html>
