<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - EcoBank</title>
    <link rel="stylesheet" href="./views/styles/styles.css">
</head>
<body>
<div class="container">
    <div class="logo-wrapper">
        <img src="./views/styles/logo.png" alt="EcoBank">
        <span class="logo-text">EcoBank</span>
    </div>
    <h1>Crear cuenta</h1>
    <?php
    if(isset($_SESSION["mensaje"])){
        echo "<div class='mensaje'>" . $_SESSION["mensaje"] . "</div>";
        unset($_SESSION["mensaje"]);
    }
    ?>
    <form method="POST" action="index.php" class="form-grid">
        <div class="input-group">
            <input type="text" name="dni" placeholder="DNI" required>
        </div>
        <div class="input-group">
            <input type="text" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="input-group">
            <input type="text" name="apellidos" placeholder="Apellidos" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>
        <div class="input-group">
            <input type="text" name="telefono" placeholder="Teléfono" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <div class="full-width">
            <input type="submit" name="registrar" value="Crear cuenta" class="btn">
        </div>
    </form>
    <div class="divider"></div>
    <form method="POST" action="index.php" class="link">
        <input type="submit" name="login" value="¿Ya tienes cuenta? Iniciar sesión">
    </form>
</div>
</body>
</html>