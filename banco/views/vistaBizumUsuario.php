<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Bizum - EcoBank</title>
    <link rel="stylesheet" href="./views/styles/styles.css">
</head>
<body class="login-body">

<div class="container">

    <div class="logo-wrapper">
        <img src="./views/styles/logo.png" alt="EcoBank">
        <span class="logo-text">EcoBank</span>
    </div>

    <h1>Enviar Bizum</h1>

    <?php
    if (isset($_SESSION["mensaje"])) {
        $tipo = $_SESSION["tipo_mensaje"] ?? "error";
        echo "<div class='mensaje $tipo'>" . $_SESSION["mensaje"] . "</div>";
        unset($_SESSION["mensaje"]);
        unset($_SESSION["tipo_mensaje"]);
    }
    ?>

    <!-- FORMULARIO PARA SOLICITAR CÓDIGO -->
    <form action="index.php" method="post" class="login-form">
        <div class="input-group">
            <input type="text" name="telefono"
                   placeholder="Teléfono del destinatario"
                   value="<?php echo $_SESSION['telefono_bizum'] ?? ''; ?>"
                   required>
        </div>

        <div class="input-group">
            <input type="number" name="importe" step="0.01" min="0.01"
                   placeholder="Importe (€)"
                   value="<?php echo $_SESSION['importe_bizum'] ?? ''; ?>"
                   required>
        </div>

        <div class="input-group">
            <input type="text" name="concepto"
                   placeholder="Concepto (opcional)"
                   value="<?php echo $_SESSION['concepto_bizum'] ?? ''; ?>">
        </div>

        <input type="hidden" name="accion" value="solicitarCodigoBizum">

        <button type="submit" class="btn">
            Solicitar código de verificación
        </button>
    </form>

    <!-- FORMULARIO PARA CONFIRMAR EL CÓDIGO -->
    <?php if (!empty($_SESSION["mostrar_codigo"])): ?>
        <div class="divider"></div>

        <form action="index.php" method="post" class="login-form">
            <div class="input-group">
                <input type="text" name="codigo"
                       placeholder="Introduce el código de verificación"
                       maxlength="5"
                       pattern="\d{5}"
                       required>
            </div>

            <input type="hidden" name="accion" value="confirmarBizum">

            <button type="submit" class="btn">
                Confirmar y Enviar Bizum
            </button>
        </form>
    <?php endif; ?>

    <div class="divider"></div>

    <form action="index.php" method="post" class="link">
        <input type="submit" name="dashboard" value="Volver al Dashboard">
    </form>

</div>

</body>
</html>
</html>