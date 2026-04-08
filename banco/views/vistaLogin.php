<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - EcoBank</title>
    <link rel="stylesheet" href="./views/styles/styles.css">
</head>
<body class="login-body">
<div class="container">
    <div class="logo-wrapper">
        <img src="./views/styles/logo.png" alt="EcoBank">
        <span class="logo-text">EcoBank</span>
    </div>
    <h1>Iniciar sesión</h1>
    <?php
    if(isset($_SESSION["mensaje"])){
        echo "<div class='mensaje'>" . $_SESSION["mensaje"] . "</div>";
        unset($_SESSION["mensaje"]);
    }
    ?>
    <form method="POST" action="index.php" class="login-form">
        <div class="input-group">
            <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>
        <input type="submit" name="iniciarSesion" value="Acceder" class="btn">
    </form>
    <div class="divider"></div>
    <form method="POST" action="index.php" class="link">
        <input type="submit" name="registro" value="¿No tienes cuenta? Crear cuenta">
    </form>
</div>
</body>
</html>