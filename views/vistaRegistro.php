<?php if(session_status()===PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoBank — Crear cuenta</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{--eco-dark:#1a3a2a;--eco-primary:#2d6a4f;--eco-mid:#40916c;--eco-light:#74c69d;--eco-pale:#d8f3dc;--eco-cream:#f6f4ef;--eco-white:#fff;--eco-text:#1c2b22;--eco-muted:#6b7c72;--eco-border:#e4ede7}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:var(--eco-cream);min-height:100vh;padding:40px 16px}
.reg-container{max-width:700px;margin:0 auto}
.reg-header{text-align:center;margin-bottom:36px}
.reg-logo{display:inline-flex;align-items:center;gap:12px;margin-bottom:24px}
.reg-logo-icon{width:44px;height:44px;background:var(--eco-primary);border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:22px}
.reg-logo-text{font-family:'Playfair Display',serif;font-size:24px;color:var(--eco-dark)}
.reg-header h2{font-size:28px;font-weight:700;color:var(--eco-text);margin-bottom:6px}
.reg-header p{font-size:14px;color:var(--eco-muted)}
.reg-card{background:var(--eco-white);border-radius:20px;padding:36px;border:1px solid var(--eco-border);box-shadow:0 4px 24px rgba(26,58,42,.07)}
.section-title{font-size:13px;font-weight:700;color:var(--eco-primary);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid var(--eco-border)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px}
.form-grid.three{grid-template-columns:1fr 1fr 1fr}
.span-2{grid-column:span 2}
.eco-label{display:block;font-size:11px;font-weight:600;color:var(--eco-muted);letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px}
.input-wrap{position:relative}
.eco-input{width:100%;padding:12px 16px 12px 40px;border:1.5px solid var(--eco-border);border-radius:10px;font-size:14px;font-family:inherit;background:var(--eco-white);color:var(--eco-text);outline:none;transition:border-color .2s,box-shadow .2s}
.eco-input.no-icon{padding-left:16px}
.eco-input:focus{border-color:var(--eco-primary);box-shadow:0 0 0 3px rgba(45,106,79,.1)}
.eco-input::placeholder{color:#aab7b0}
.input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--eco-muted);font-size:14px;pointer-events:none}
.pass-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--eco-muted);cursor:pointer;font-size:14px;background:none;border:none}
.eco-btn-reg{width:100%;padding:14px;border-radius:11px;background:var(--eco-primary);color:#fff;font-size:15px;font-weight:600;font-family:inherit;border:none;cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px}
.eco-btn-reg:hover{background:var(--eco-dark);box-shadow:0 6px 20px rgba(45,106,79,.3)}
.alert-box{padding:13px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.alert-error{background:#fde8ea;color:#7f1d1d;border:1px solid #f5a3a9}
.alert-success{background:#d8f3dc;color:#1a3a2a;border:1px solid #74c69d}
.form-footer{text-align:center;margin-top:20px;font-size:13px;color:var(--eco-muted)}
.password-hint{font-size:11px;color:var(--eco-muted);margin-top:5px}
@media(max-width:600px){.form-grid,.form-grid.three{grid-template-columns:1fr}.span-2{grid-column:span 1}}
</style>
</head>
<body>
<div class="reg-container">
  <div class="reg-header">
      <div class="reg-logo" style="flex-direction:column; gap:10px;">
          <img src="views/styles/logo.png" alt="Logo EcoBank" style="width:120px;">
      </div>
    <h2>Crear cuenta</h2>
    <p>Únete a EcoBank y gestiona tus finanzas de forma sostenible.</p>
  </div>

  <?php if(isset($_SESSION["mensaje"])): ?>
  <div class="alert-box alert-error"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($_SESSION["mensaje"]) ?></div>
  <?php unset($_SESSION["mensaje"]); endif; ?>

  <div class="reg-card">
    <form method="POST" action="index.php">
      <!-- DATOS PERSONALES -->
      <div class="section-title"><i class="bi bi-person-fill me-2"></i>Datos personales</div>
      <div class="form-grid">
        <div>
          <label class="eco-label">Nombre *</label>
          <div class="input-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" name="nombre" class="eco-input" placeholder="Tu nombre" required>
          </div>
        </div>
        <div>
          <label class="eco-label">Apellidos *</label>
          <div class="input-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" name="apellidos" class="eco-input" placeholder="Tus apellidos" required>
          </div>
        </div>
        <div>
          <label class="eco-label">DNI / NIE *</label>
          <div class="input-wrap">
            <i class="bi bi-credit-card input-icon"></i>
            <input type="text" name="dni" class="eco-input" placeholder="12345678A" required maxlength="10">
          </div>
        </div>
        <div>
          <label class="eco-label">Fecha de nacimiento</label>
          <div>
            <input type="date" name="fecha_nacimiento" class="eco-input" required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" title="Debes tener al menos 18 años para registrarte">
          </div>
        </div>
      </div>

      <!-- CONTACTO -->
      <div class="section-title" style="margin-top:8px"><i class="bi bi-envelope-fill me-2"></i>Contacto</div>
      <div class="form-grid">
        <div class="span-2">
          <label class="eco-label">Email *</label>
          <div class="input-wrap">
            <i class="bi bi-envelope input-icon"></i>
            <input type="email" name="email" class="eco-input" placeholder="tu@email.com" required autocomplete="email">
          </div>
        </div>
        <div>
          <label class="eco-label">Teléfono *</label>
          <div class="input-wrap">
            <i class="bi bi-phone input-icon"></i>
            <input type="tel" name="telefono" class="eco-input" placeholder="678478578" required>
          </div>
        </div>
      </div>

      <!-- DIRECCIÓN -->
      <div class="section-title" style="margin-top:8px"><i class="bi bi-geo-alt-fill me-2"></i>Dirección</div>
      <div class="form-grid">
        <div>
          <label class="eco-label">Provincia</label>
            <select name="provincia" id="provincia" class="eco-input no-icon"></select>
        </div>
        <div>
          <label class="eco-label">Municipio</label>
            <select name="municipio" id="municipio" class="eco-input no-icon"></select>
        </div>
        <div class="span-2">
          <label class="eco-label">Vía (calle, avenida...)</label>
            <select name="via" id="via" class="eco-input no-icon"></select>        </div>
        <div>
          <label class="eco-label">Nº / Piso</label>
          <input type="text" name="numero_piso" class="eco-input no-icon" placeholder="Nº3, 2ºA">
        </div>
      </div>

      <!-- CONTRASEÑA -->
      <div class="section-title" style="margin-top:8px"><i class="bi bi-lock-fill me-2"></i>Contraseña</div>
      <div class="form-grid">
        <div>
          <label class="eco-label">Contraseña *</label>
          <div class="input-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="password" id="p1" class="eco-input" placeholder="Mín. 6 caracteres" required minlength="6">
            <button type="button" class="pass-toggle" onclick="tp('p1','e1')"><i class="bi bi-eye" id="e1"></i></button>
          </div>
        </div>
        <div>
          <label class="eco-label">Repetir contraseña *</label>
          <div class="input-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input type="password" name="cpassword" id="p2" class="eco-input" placeholder="Repite la contraseña" required minlength="6">
            <button type="button" class="pass-toggle" onclick="tp('p2','e2')"><i class="bi bi-eye" id="e2"></i></button>
          </div>
        </div>
      </div>

      <button type="submit" name="registrar" class="eco-btn-reg">
          Crear cuenta EcoBank
      </button>
    </form>
  </div>

  <div class="form-footer">
    ¿Ya tienes cuenta?
    <form method="POST" action="index.php" style="display:inline">
      <button type="submit" name="login" style="background:none;border:none;padding:0;cursor:pointer;color:var(--eco-primary);font-weight:600;font-family:inherit;font-size:13px">Iniciar sesión</button>
    </form>
  </div>
</div>

<script>
function tp(fid,iid){
  const f=document.getElementById(fid),i=document.getElementById(iid);
  f.type=f.type==='password'?'text':'password';
  i.className=f.type==='password'?'bi bi-eye':'bi bi-eye-slash';
}

    document.addEventListener("DOMContentLoaded", () => {

    const provincia = document.getElementById("provincia");
    const municipio = document.getElementById("municipio");
    const via = document.getElementById("via");

    // 1. Cargar provincias
        fetch("controllers/api/catastro.php", {    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "accion=provincias"
})
    .then(res => res.json())
    .then(data => {
    provincia.innerHTML = '<option value="">Selecciona provincia</option>';
    data.forEach(p => {
    provincia.innerHTML += `<option value="${p}">${p}</option>`;
});
});

    // 2. Cuando eliges provincia → municipios
    provincia.addEventListener("change", () => {
        fetch("controllers/api/catastro.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `accion=municipios&provincia=${provincia.value}`
})
    .then(res => res.json())
    .then(data => {
    municipio.innerHTML = '<option value="">Selecciona municipio</option>';
    data.forEach(m => {
    municipio.innerHTML += `<option value="${m}">${m}</option>`;
});
});
});

    // 3. Cuando eliges municipio → vías
    municipio.addEventListener("change", () => {
        fetch("controllers/api/catastro.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `accion=vias&provincia=${provincia.value}&municipio=${municipio.value}`
})
    .then(res => res.json())
    .then(data => {
    via.innerHTML = '<option value="">Selecciona vía</option>';
    data.forEach(v => {
    via.innerHTML += `<option value="${v}">${v}</option>`;
});
});
});

});

</script>
</body>
</html>
