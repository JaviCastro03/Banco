
<?php
require_once "./models/ConexionDB.php";
require_once "./models/UsuarioDDBB.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$conexion = new UsuarioDDBB();
if (isset($_POST["ajax"])) {

    if ($_POST["accion"] == "buscar") {
        $usuarios = $conexion->getUsuarios($_POST["busqueda"]);
        echo json_encode($usuarios);
        exit;
    }

    if ($_POST["accion"] == "actualizar") {
        $ok = $conexion->actualizarUsuario(
            $_POST["id"],
            $_POST["nombre"],
            $_POST["apellidos"],
            $_POST["email"],
            $_POST["telefono"],
            $_POST["rol"]
        );
        echo json_encode(["ok" => $ok]);
        exit;
    }

    if ($_POST["accion"] == "eliminar") {
        $ok = $conexion->eliminarUsuario($_POST["id"]);
        echo json_encode(["ok" => $ok]);
        exit;
    }
    if ($_POST["accion"] == "historial") {
        $historial = $conexion->getHistorialUsuario($_POST["id"]);
        echo json_encode($historial);
        exit;
    }
}
if (!isset($_SESSION["ruta"])) {
    $_SESSION["ruta"] = "login";
}

if (isset($_POST["dashboard"])) $_SESSION["ruta"] = "dashboard";
if (isset($_POST["login"])) $_SESSION["ruta"] = "login";
if (isset($_POST["registro"])) $_SESSION["ruta"] = "registro";


if ($_SESSION["ruta"] == "login") {

    if (isset($_POST["iniciarSesion"])) {

        if (!$conexion->emailExiste($_POST["email"])) {
            $_SESSION["mensaje"] = "Credenciales incorrectas";
            header("Location: index.php");
            exit;
        }

        $password = $conexion->getPasswordHash($_POST["email"]);

        if (!password_verify($_POST["password"], $password)) {
            $_SESSION["mensaje"] = "Credenciales incorrectas";
            header("Location: index.php");
            exit;
        }

        $rol = $conexion->getRol($_POST["email"]);

        if ($rol == 0) {
            $_SESSION["mensaje"] = "Debes verificar tu correo";
            header("Location: index.php");
            exit;
        }

        $_SESSION["idU"] = $conexion->getIdUsuario($_POST["email"]);
        $_SESSION["nombre"] = $conexion->getNombreUsuario($_POST["email"]);
        $_SESSION["email"] = $_POST["email"];
        $_SESSION["rol"] = $rol;

        if ($rol == 2) {
            $_SESSION["ruta"] = "admin";
        } else {
            $_SESSION["ruta"] = "dashboard";
        }

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
                        ADMIN
====================================================== */
if ($_SESSION["ruta"] == "admin") {

    if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) {
        $_SESSION["ruta"] = "login";
        header("Location: index.php");
        exit;
    }

    require_once "./views/vistaDashboardAdmin.php";
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
        if ($conexion->dniExiste($_POST["dni"])) {
            $_SESSION["mensaje"] = "El DNI ya está registrado";
            header("Location: index.php");
            exit;
        }

        if ($conexion->insertarUsuario(
            $_POST["dni"],
            $_POST["nombre"],
            $_POST["apellidos"],
            $_POST["email"],
            $_POST["telefono"] ?? '',
            $_POST["password"],
            $_POST["fecha_nacimiento"] ?? null,
            $_POST["provincia"],
            $_POST["municipio"],
            $_POST["calle"],
            $_POST["numero_piso"]
        )) {

            $mensaje = "Pulsa en el enlace para verificar tu cuenta:<br>
            <a href='http://localhost/banco/banco/controllers/verificar/verificar.php?email="
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
