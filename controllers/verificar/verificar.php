<?php

require_once "../../models/UsuarioDDBB.php";
require_once "../../models/ConexionDB.php";

session_start();

$conexion = new UsuarioDDBB();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET["email"]) && $conexion->setRol($_GET["email"], 1)) {

        $_SESSION["mensaje"] = "Usuario verificado correctamente";
        $_SESSION["tipo_mensaje"] = "success";

        header("Location: ../../index.php");
        exit;

    } else {

        $_SESSION["mensaje"] = "Error al verificar el usuario";
        $_SESSION["tipo_mensaje"] = "error";

        header("Location: ../../index.php");
        exit;
    }

} else {
    header("Location: ../../index.php");
    exit;
}