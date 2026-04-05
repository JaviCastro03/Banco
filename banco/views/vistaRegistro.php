<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Registro</h1>
    <?php
    if(isset($_SESSION["mensaje"])){
        echo $_SESSION["mensaje"];
        unset($_SESSION["mensaje"]);
    }
    ?>
    <form method="POST" action="index.php"><br>
        <input type="text" name="dni" placeholder="Dni"><br>
        <input type="text" name="nombre" placeholder="Nombre"><br>
        <input type="text" name="apellidos" placeholder="Apellidos"><br>
        <input type="text" name="email" placeholder="Email"><br>
        <input type="password" name="password" placeholder="Contraseña"><br>
        <input type="password" name="cpassword" placeholder="Confirmar contraseña"><br>
        <input type="submit" name="registrar" value="Registrar"><br>
    </form>
    <form method="POST" action="index.php">
        <input type="submit" name="login" value="¿Ya tienes una cuenta?">
    </form>
</body>
</html>