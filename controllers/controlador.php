<?php
require_once "./models/ConexionDB.php";
require_once "./models/UsuarioDDBB.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$conexion = new UsuarioDDBB();

/* ======================================================
                    PETICIONES AJAX
====================================================== */
if (isset($_POST["ajax"])) {
    header('Content-Type: application/json');
    $accion = $_POST["accion"] ?? "";

    // ADMIN
    if ($accion === "buscar")    { echo json_encode($conexion->getUsuarios($_POST["busqueda"] ?? "")); exit; }
    if ($accion === "actualizar"){ echo json_encode(["ok" => $conexion->actualizarUsuario($_POST["id"],$_POST["nombre"],$_POST["apellidos"],$_POST["email"],$_POST["telefono"],$_POST["rol"])]); exit; }
    if ($accion === "eliminar")  { echo json_encode(["ok" => $conexion->eliminarUsuario($_POST["id"])]); exit; }
    if ($accion === "historial") { echo json_encode($conexion->getHistorialUsuario($_POST["id"])); exit; }

    if ($accion === "adminResumen") {
        if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true, "datos"=>$conexion->getResumenAdmin()]); exit;
    }

    if ($accion === "adminHistorialConCuenta") {
        if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) { echo json_encode(["ok"=>false]); exit; }
        $id = intval($_POST["id"] ?? 0);
        echo json_encode([
            "ok"       => true,
            "historial"=> $conexion->getHistorialUsuario($id),
            "cuenta"   => $conexion->getCuentaUsuario($id)
        ]); exit;
    }

    if ($accion === "adminCuentas") {
        if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true, "cuentas"=>$conexion->getAllCuentas()]); exit;
    }

    if ($accion === "adminMovimientos") {
        if (!isset($_SESSION["idU"]) || ($_SESSION["rol"] ?? 0) != 2) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode([
            "ok"         => true,
            "movimientos"=> $conexion->getAllMovimientos(),
            "stats"      => $conexion->getStatsMovimientos()
        ]); exit;
    }

    // USUARIO
    if ($accion === "getDashboard") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode([
            "ok"         => true,
            "datos"      => $conexion->getDatosUsuario($_SESSION["idU"]),
            "resumen"    => $conexion->getResumenMes($_SESSION["idU"]),
            "movimientos"=> $conexion->getUltimosMovimientos($_SESSION["idU"], 5)
        ]); exit;
    }

    if ($accion === "getHistorial") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true,"historial"=>$conexion->getHistorialUsuario($_SESSION["idU"])]); exit;
    }
    if ($accion === "ultimosBizum") {
        if (!isset($_SESSION["idU"])) {
            echo json_encode(["ok"=>false]);
            exit;
        }

        echo json_encode([
            "ok" => true,
            "datos" => $conexion->getUltimosBizum($_SESSION["idU"])
        ]);
        exit;
    }

    if ($accion === "ultimasTransferencias") {
        if (!isset($_SESSION["idU"])) {
            echo json_encode(["ok"=>false]);
            exit;
        }

        echo json_encode([
            "ok" => true,
            "datos" => $conexion->getUltimasTransferencias($_SESSION["idU"])
        ]);
        exit;
    }

    if ($accion === "getGrafica") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $filtro = $_POST["filtro"] ?? "meses";
        echo json_encode(["ok"=>true,"datos"=>$conexion->getDatosGrafica($_SESSION["idU"], $filtro)]); exit;
    }

    if ($accion === "getTarjeta") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true,"tarjeta"=>$conexion->getTarjetaUsuario($_SESSION["idU"])]); exit;
    }

    if ($accion === "crearTarjeta") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $u = $conexion->getDatosUsuario($_SESSION["idU"]);
        if (!$u||!$u['cuenta_id']) { echo json_encode(["ok"=>false,"mensaje"=>"Sin cuenta bancaria."]); exit; }
        $titular = strtoupper(trim($u['nombre'])." ".trim($u['apellidos']));
        $t = $conexion->crearTarjeta($u['cuenta_id'], $titular);
        echo json_encode(["ok"=>true,"tarjeta"=>$t,"mensaje"=>"Tarjeta creada correctamente."]); exit;
    }

    if ($accion === "actualizarPerfil") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $ok = $conexion->actualizarPerfil($_SESSION["idU"],
            $_POST["nombre"]??'', $_POST["apellidos"]??'', $_POST["email"]??'',
            $_POST["telefono"]??'', $_POST["provincia"]??'', $_POST["municipio"]??'',
            $_POST["via"]??'', $_POST["numero_piso"]??'');
        if ($ok) { $_SESSION["nombre"]=$_POST["nombre"]??$_SESSION["nombre"]; $_SESSION["email"]=$_POST["email"]??$_SESSION["email"]; }
        echo json_encode(["ok"=>$ok,"mensaje"=>$ok?"Perfil actualizado.":"Error al actualizar."]); exit;
    }

    if ($accion === "cambiarPassword") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode($conexion->cambiarPassword($_SESSION["idU"],$_POST["password_actual"]??'',$_POST["nueva_password"]??'')); exit;
    }

    if ($accion === "getNotificaciones") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true,"notificaciones"=>$conexion->getNotificaciones($_SESSION["idU"]),"no_leidas"=>$conexion->contarNoLeidas($_SESSION["idU"])]); exit;
    }

    if ($accion === "marcarLeidas") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $conexion->marcarNotificacionesLeidas($_SESSION["idU"]);
        echo json_encode(["ok"=>true]); exit;
    }

    // BIZUM
    if ($accion === "solicitarCodigoBizum") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false,"mensaje"=>"Sesión no válida."]); exit; }
        $telefono = trim($_POST["telefono"]??""); $importe = floatval($_POST["importe"]??0); $concepto = trim($_POST["concepto"]??"Bizum");
        if (!$telefono||$importe<=0) { echo json_encode(["ok"=>false,"mensaje"=>"Datos inválidos."]); exit; }
        $codigo = $conexion->generarCodigoVerificacion($_SESSION["idU"]);
        $importeF = number_format($importe,2,',','.');
        enviarCorreoGmail($_SESSION["email"],"Código Bizum - EcoBank","<div style='font-family:sans-serif;padding:20px'><h2 style='color:#1a3a2a'>Código de Verificación Bizum</h2><p>Bizum de <b>€$importeF</b> a <b>$telefono</b>. Concepto: <b>$concepto</b></p><div style='font-size:36px;font-weight:bold;letter-spacing:10px;color:#2d6a4f;text-align:center;padding:24px;background:#f0f7f4;border-radius:8px;margin:16px 0'>$codigo</div><p style='color:#888;font-size:12px'>Válido 10 minutos. Si no fuiste tú, ignora este email.</p></div>");
        $_SESSION["telefono_bizum"]=$telefono; $_SESSION["importe_bizum"]=$importe; $_SESSION["concepto_bizum"]=$concepto;
        echo json_encode(["ok"=>true,"mensaje"=>"Código enviado a ".$_SESSION["email"]]); exit;
    }

    if ($accion === "confirmarBizum") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false,"mensaje"=>"Sesión no válida."]); exit; }
        if (!$conexion->verificarCodigo($_SESSION["idU"],trim($_POST["codigo"]??""))) { echo json_encode(["ok"=>false,"mensaje"=>"Código incorrecto o caducado."]); exit; }
        $r = $conexion->enviarBizum($_SESSION["idU"],$_SESSION["telefono_bizum"],$_SESSION["importe_bizum"],$_SESSION["concepto_bizum"]);
        unset($_SESSION["telefono_bizum"],$_SESSION["importe_bizum"],$_SESSION["concepto_bizum"]);
        echo json_encode($r); exit;
    }

    // TRANSFERENCIA
    if ($accion === "solicitarCodigoTransferencia") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false,"mensaje"=>"Sesión no válida."]); exit; }
        $iban=$_POST["iban_destino"]??""; $titular=$_POST["titular_destino"]??""; $importe=floatval($_POST["importe"]??0); $concepto=$_POST["concepto"]??"Transferencia";
        if (!$iban||$importe<=0||!$titular) { echo json_encode(["ok"=>false,"mensaje"=>"Datos inválidos."]); exit; }
        if (!UsuarioDDBB::validarIBAN($iban)) { echo json_encode(["ok"=>false,"mensaje"=>"El IBAN introducido no es válido."]); exit; }
        $codigo=$conexion->generarCodigoVerificacion($_SESSION["idU"]);
        $importeF=number_format($importe,2,',','.');
        enviarCorreoGmail($_SESSION["email"],"Código Transferencia - EcoBank","<div style='font-family:sans-serif;padding:20px'><h2 style='color:#1a3a2a'>Código de Verificación Transferencia</h2><p>Transferencia de <b>€$importeF</b> a <b>$titular</b> (IBAN: $iban)</p><div style='font-size:36px;font-weight:bold;letter-spacing:10px;color:#2d6a4f;text-align:center;padding:24px;background:#f0f7f4;border-radius:8px;margin:16px 0'>$codigo</div><p style='color:#888;font-size:12px'>Válido 10 minutos.</p></div>");
        $_SESSION["iban_trans"]=$iban; $_SESSION["titular_trans"]=$titular; $_SESSION["importe_trans"]=$importe; $_SESSION["concepto_trans"]=$concepto;
        echo json_encode(["ok"=>true,"mensaje"=>"Código enviado a ".$_SESSION["email"]]); exit;
    }

    if ($accion === "confirmarTransferencia") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false,"mensaje"=>"Sesión no válida."]); exit; }
        if (!$conexion->verificarCodigo($_SESSION["idU"],trim($_POST["codigo"]??""))) { echo json_encode(["ok"=>false,"mensaje"=>"Código incorrecto o caducado."]); exit; }
        $r=$conexion->enviarTransferencia($_SESSION["idU"],$_SESSION["iban_trans"],$_SESSION["titular_trans"],$_SESSION["importe_trans"],$_SESSION["concepto_trans"]);
        $transaccion_id = $r["transaccion_id"] ?? null;
        unset($_SESSION["iban_trans"],$_SESSION["titular_trans"],$_SESSION["importe_trans"],$_SESSION["concepto_trans"]);
        echo json_encode($r); exit;
    }

    // SIMULACIONES
    if ($accion === "getSimulaciones") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        echo json_encode(["ok"=>true,"simulaciones"=>$conexion->getSimulaciones($_SESSION["idU"])]); exit;
    }

    if ($accion === "crearSimulacion") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $nombre = trim($_POST["nombre"] ?? "");
        $tipo = $_POST["tipo"] ?? "gasto";
        $importe = floatval($_POST["importe"] ?? 0);
        $intervalo = intval($_POST["intervalo_dias"] ?? 1);
        if (!$nombre || $intervalo < 1) { echo json_encode(["ok"=>false,"mensaje"=>"Datos inválidos."]); exit; }
        $id = $conexion->crearSimulacion($_SESSION["idU"], $nombre, $tipo, $importe, $intervalo);
        echo json_encode(["ok"=>true,"id"=>$id,"mensaje"=>"Simulación creada."]); exit;
    }

    if ($accion === "eliminarSimulacion") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $ok = $conexion->eliminarSimulacion(intval($_POST["id"]??0), $_SESSION["idU"]);
        echo json_encode(["ok"=>$ok]); exit;
    }

    if ($accion === "toggleSimulacion") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $ok = $conexion->toggleSimulacion(intval($_POST["id"]??0), $_SESSION["idU"]);
        echo json_encode(["ok"=>$ok]); exit;
    }

    if ($accion === "procesarSimulaciones") {
        if (!isset($_SESSION["idU"])) { echo json_encode(["ok"=>false]); exit; }
        $r = $conexion->procesarSimulaciones($_SESSION["idU"]);
        echo json_encode($r); exit;
    }
}

/* ======================================================
                        RUTAS
====================================================== */
if (!isset($_SESSION["ruta"])) $_SESSION["ruta"] = "login";
if (isset($_POST["dashboard"])) $_SESSION["ruta"] = "dashboard";
if (isset($_POST["login"]))     $_SESSION["ruta"] = "login";
if (isset($_POST["registro"]))  $_SESSION["ruta"] = "registro";
if (isset($_POST["bizum"]))     $_SESSION["ruta"] = "bizumUsuario";

if ($_SESSION["ruta"] === "login") {
    if (isset($_POST["iniciarSesion"])) {
        if (!$conexion->emailExiste($_POST["email"])) { $_SESSION["mensaje"]="Credenciales incorrectas"; header("Location: index.php"); exit; }
        if (!password_verify($_POST["password"], $conexion->getPasswordHash($_POST["email"]))) { $_SESSION["mensaje"]="Credenciales incorrectas"; header("Location: index.php"); exit; }
        $rol = $conexion->getRol($_POST["email"]);
        if ($rol == 0) { $_SESSION["mensaje"]="Debes verificar tu correo"; header("Location: index.php"); exit; }
        $_SESSION["idU"]=$conexion->getIdUsuario($_POST["email"]); $_SESSION["nombre"]=$conexion->getNombreUsuario($_POST["email"]); $_SESSION["email"]=$_POST["email"]; $_SESSION["rol"]=$rol;
        $_SESSION["ruta"]=($rol==2)?"admin":"dashboard";
        header("Location: index.php"); exit;
    }
    require_once "./views/vistaLogin.php";
}

if ($_SESSION["ruta"] === "dashboard") {
    if (!isset($_SESSION["idU"])) { $_SESSION["ruta"]="login"; header("Location: index.php"); exit; }
    require_once "./views/vistaDashboardUsuario.php";
}

if ($_SESSION["ruta"] === "admin") {
    if (!isset($_SESSION["idU"])||($_SESSION["rol"]??0)!=2) { $_SESSION["ruta"]="login"; header("Location: index.php"); exit; }
    require_once "./views/vistaDashboardAdmin.php";
}

if ($_SESSION["ruta"] === "registro") {
    if (isset($_POST["registrar"])) {
        if ($conexion->emailExiste($_POST["email"])) { $_SESSION["mensaje"]="El correo ya existe"; header("Location: index.php"); exit; }
        if (!UsuarioDDBB::comprobarPassword($_POST["password"],$_POST["cpassword"])) { $_SESSION["mensaje"]="Las contraseñas no coinciden"; header("Location: index.php"); exit; }
        if ($conexion->dniExiste($_POST["dni"])) { $_SESSION["mensaje"]="El DNI ya está registrado"; header("Location: index.php"); exit; }
        // Verificar que el usuario tenga al menos 18 años
        $fechaNac = $_POST["fecha_nacimiento"] ?? null;
        if (!$fechaNac) { $_SESSION["mensaje"]="La fecha de nacimiento es obligatoria"; header("Location: index.php"); exit; }
        $hoy = new DateTime();
        $nacimiento = new DateTime($fechaNac);
        $edad = $hoy->diff($nacimiento)->y;
        if ($edad < 18) { $_SESSION["mensaje"]="Debes tener al menos 18 años para crear una cuenta"; header("Location: index.php"); exit; }
        if ($conexion->insertarUsuario($_POST["dni"],$_POST["nombre"],$_POST["apellidos"],$_POST["email"],$_POST["telefono"]??'',$_POST["password"],$_POST["fecha_nacimiento"]??null,$_POST["provincia"],$_POST["municipio"],$_POST["via"],$_POST["numero_piso"])) {
            $msg="<div style='font-family:sans-serif;padding:20px'><h2 style='color:#1a3a2a'>Verifica tu cuenta EcoBank</h2><p>Hola <b>".$_POST["nombre"]."</b>, pulsa para activar tu cuenta:</p><a href='http://localhost/banco/controllers/verificar/verificar.php?email=".urlencode($_POST["email"])."' style='display:inline-block;padding:12px 24px;background:#2d6a4f;color:white;border-radius:8px;text-decoration:none;font-weight:bold;margin-top:12px'>Verificar cuenta</a></div>";
            $_SESSION["mensaje"] = enviarCorreoGmail($_POST["email"],"Verifica tu cuenta EcoBank",$msg) ? "Revisa tu correo para activar la cuenta" : "No se pudo enviar el correo";
        } else { $_SESSION["mensaje"]="Error al registrar usuario"; }
        header("Location: index.php"); exit;
    }
    require_once "./views/vistaRegistro.php";
}

function enviarCorreoGmail($email,$asunto,$mensaje) {
    try {
        require './models/vendor/autoload.php';
        $mail=new PHPMailer(true); $mail->isSMTP(); $mail->Host='smtp.gmail.com'; $mail->SMTPAuth=true;
        $mail->Username='jcastro2gs@gmail.com'; $mail->Password='REMOVED';
        $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS; $mail->Port=587;
        $mail->setFrom('jcastro2gs@gmail.com','EcoBank'); $mail->addAddress($email);
        $mail->isHTML(true); $mail->Subject=$asunto; $mail->Body=$mensaje; $mail->CharSet='UTF-8';
        $mail->send(); return true;
    } catch(Exception $e) { return false; }
}
?>