<?php
require_once "./models/ConexionDB.php";
require_once "./models/UsuarioDDBB.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$conexion = new UsuarioDDBB();

if (!isset($_SESSION["ruta"])) {
    $_SESSION["ruta"] = "login";
}

// navegación
if (isset($_POST["dashboard"])) $_SESSION["ruta"] = "dashboard";
if (isset($_POST["login"])) $_SESSION["ruta"] = "login";
if (isset($_POST["registro"])) $_SESSION["ruta"] = "registro";


/* ======================================================
                        LOGIN
====================================================== */
if ($_SESSION["ruta"] == "login") {

    if (isset($_POST["iniciarSesion"])) {

        if (!$conexion->emailExiste($_POST["email"])) {
            $_SESSION["mensaje"] = "Credenciales incorrectas";
            header("Location: index.php");
            exit;
        }

        // ✅ CAMBIO AQUÍ
        $password = $conexion->getPasswordHash($_POST["email"]);

        if (!password_verify($_POST["password"], $password)) {
            $_SESSION["mensaje"] = "Credenciales incorrectas";
            header("Location: index.php");
            exit;
        }

        // 🚨 NO VERIFICADO (activo = 0 por ejemplo)
        if (!$conexion->getIdUsuario($_POST["email"])) {
            $_SESSION["mensaje"] = "Debes verificar tu correo";
            header("Location: index.php");
            exit;
        }

        // login correcto
        $_SESSION["idU"] = $conexion->getIdUsuario($_POST["email"]);
        $_SESSION["nombre"] = $conexion->getNombreUsuario($_POST["email"]);
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["ruta"] = "dashboard";

        header("Location: index.php");
        exit;
    }

    require_once "./views/vistaLogin.php";
}



/* ======================================================
                        DASHBOARD
====================================================== */
if ($_SESSION["ruta"] == "dashboard") {

    if (!isset($_SESSION["idU"])) {
        $_SESSION["ruta"] = "login";
        header("Location: index.php");
        exit;
    }

    require_once "./views/vistaDashboardUsuario.php";
}



/* ======================================================
                        REGISTRO
====================================================== */
if ($_SESSION["ruta"] == "registro") {

    if (isset($_POST["registrar"])) {

        if ($conexion->emailExiste($_POST["email"])) {
            $_SESSION["mensaje"] = "El correo ya existe";
            header("Location: index.php");
            exit;
        }

        if (!UsuarioDDBB::comprobarPassword($_POST["password"], $_POST["cpassword"])) {
            $_SESSION["mensaje"] = "Las contraseñas no coinciden";
            header("Location: index.php");
            exit;
        }

        // ❌ ELIMINADO HASH AQUÍ (ya se hace en el modelo)

        if ($conexion->insertarUsuario(
            $_POST["dni"],
            $_POST["nombre"],
            $_POST["apellidos"],
            $_POST["email"],
            $_POST["telefono"] ?? '',
            $_POST["password"],
            $_POST["direccion"] ?? null,
            $_POST["fecha_nacimiento"] ?? null
        )) {

            $mensaje = "Pulsa en el enlace para verificar tu cuenta:<br>
            <a href='http://localhost/banco/controllers/verificar/verificar.php?email="
                . $_POST["email"] . "'>Verificar cuenta</a>";

            if (enviarCorreoGmail($_POST["email"], "Verificación de cuenta", $mensaje)) {
                $_SESSION["mensaje"] = "Revisa tu correo para activar la cuenta";
            } else {
                $_SESSION["mensaje"] = "No se pudo enviar el correo";
            }

        } else {
            $_SESSION["mensaje"] = "Error al registrar usuario";
        }

        header("Location: index.php");
        exit;
    }

    require_once "./views/vistaRegistro.php";
}



/* ======================================================
                    ENVIAR CORREO
====================================================== */
function enviarCorreoGmail($email, $asunto, $mensaje)
{
    try {
        require './models/vendor/autoload.php';

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jcastro2gs@gmail.com';
        $mail->Password = 'REMOVED';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jcastro2gs@gmail.com', 'Aplicación');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $mail->CharSet = 'UTF-8';

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}