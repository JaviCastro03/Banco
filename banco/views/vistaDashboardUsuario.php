<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

<h1>Bienvenido usuario <?php echo $_SESSION["nombre"] ?? "usuario"; ?></h1>

<form action="index.php" method="post">
    <button type="submit" name="bizum">Enviar Bizum</button>
</form>

<br>

<form action="logout.php" method="post">
    <button type="submit">Cerrar sesión</button>
</form>

</body>
</html>
</html>