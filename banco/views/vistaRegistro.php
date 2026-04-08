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
        $clase = strpos($_SESSION["mensaje"], "correctamente") !== false ? "success" : "error";
        echo "<div class='mensaje $clase'>" . $_SESSION["mensaje"] . "</div>";
        unset($_SESSION["mensaje"]);
    }
    ?>

    <form method="POST" action="index.php" class="form-grid">

        <!-- =========================
            DATOS PERSONALES
        ========================= -->
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
            <input type="text" name="telefono" placeholder="Teléfono" required>
        </div>


        <!-- =========================
            EMAIL GRANDE
        ========================= -->
        <div class="input-group full-width">
            <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>




        <div class="input-group">
            <select id="provincia" name="provincia" required>
                <option value="">Provincia</option>
            </select>
        </div>

        <div class="input-group">
            <select id="municipio" name="municipio" required>
                <option value="">Municipio</option>
            </select>
        </div>

        <div class="input-group full-width">
            <select id="via" name="via" required>
                <option value="">Vía</option>
            </select>
        </div>

        <div class="input-group full-width">
            <input type="text" name="numero_piso" placeholder="Número / Piso" required>
        </div>


        <!-- =========================
            CONTRASEÑA
        ========================= -->
        <div class="input-group">
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>

        <div class="input-group">
            <input type="password" name="cpassword" placeholder="Confirmar contraseña" required>
        </div>


        <!-- BOTÓN -->
        <div class="full-width">
            <input type="submit" name="registrar" value="Crear cuenta" class="btn">
        </div>

    </form>

    <div class="divider"></div>

    <form method="POST" action="index.php" class="link">
        <input type="submit" name="login" value="¿Ya tienes cuenta? Iniciar sesión">
    </form>

</div>
<script src="./views/js/catastro.js"></script>

</body>
</html>