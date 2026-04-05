<?php

require_once "../../models/UsuarioDDBB.php";
require "../../models/ConexionDB.php";

session_start();
$conexion = new UsuarioDDBB();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if(isset($_GET["email"]) && $conexion->setRol($_GET["email"], 1)) {

        echo "Verificación exitosa <br>";
        echo "Serás redirigido en 5 segundos...";
        $_SESSION["mensaje"] = "<p id='success'>Usuario verificado correctamente</p>";

        header("Refresh: 5; URL=../../index.php");
        exit;
    } else {
        echo "Error al verificar el usuario";
        header("Refresh: 5; URL=../../index.php");
        exit;
    }

} else {
    header("location: ../../index.php");
    exit();
}