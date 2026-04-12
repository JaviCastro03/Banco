<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - EcoBank</title>
    <link rel="stylesheet" href="views/styles/styles.css">
</head>
<body>

<?php require_once "views/cabecera.php"; ?>

<div class="container">
    <h1>Panel de Usuario</h1>
    <p>Gestiona tus operaciones bancarias de forma rápida y segura.</p>

    <form action="index.php" method="post">
        <button type="submit" name="bizum" class="btn">Enviar Bizum</button>
    </form>
</div>

</body>
</html>