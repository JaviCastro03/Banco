<?php
require_once "../../models/vendor_old/econea/nusoap/src/nusoap.php";

header('Content-Type: application/json; charset=utf-8');

$url = "https://ovc.catastro.meh.es/ovcservweb/ovcswlocalizacionrc/ovccallejero.asmx?WSDL";

$cliente = new nusoap_client($url, true);
$cliente->soap_defencoding = "UTF-8";
$cliente->decode_utf8 = false;

$accion = $_POST['accion'] ?? '';

if ($accion == 'provincias') {
    echo json_encode(obtenerProvincias($cliente), JSON_UNESCAPED_UNICODE);
}

if ($accion == 'municipios') {
    $provincia = $_POST['provincia'] ?? '';
    echo json_encode(obtenerMunicipios($cliente, $provincia), JSON_UNESCAPED_UNICODE);
}

if ($accion == 'vias') {
    $provincia = $_POST['provincia'] ?? '';
    $municipio = $_POST['municipio'] ?? '';
    echo json_encode(obtenerVias($cliente, $provincia, $municipio), JSON_UNESCAPED_UNICODE);
}

/* ================= FUNCIONES ================= */

function obtenerProvincias($cliente){

    $respuesta = $cliente->call("ObtenerProvincias", "");
    $vector = $respuesta['consulta_provinciero']['provinciero']['prov'];

    $provincias = [];

    for ($i = 0; $i < count($vector); $i++) {
        $provincias[] = $vector[$i]['np'];
    }

    return $provincias;
}

function obtenerMunicipios($cliente, $provincia){

    $parametros = array(
        "Provincia" => $provincia,
        "Municipio" => ""
    );

    $respuesta = $cliente->call("ObtenerMunicipios", $parametros);
    $vector = $respuesta['consulta_municipiero']['municipiero']['muni'];

    $municipios = [];

    for ($i = 0; $i < count($vector); $i++) {
        $municipios[] = $vector[$i]['nm'];
    }

    return $municipios;
}

function obtenerVias($cliente, $provincia, $municipio){

    $parametros = array(
        "Provincia" => $provincia,
        "Municipio" => $municipio,
        "TipoVia" => "",
        "NombresVia" => ""
    );

    $respuesta = $cliente->call("ObtenerCallejero", $parametros);
    $vector = $respuesta['consulta_callejero']['callejero']['calle'];

    $vias = [];

    for ($i = 0; $i < count($vector); $i++) {
        $tipoVia   = $vector[$i]['dir']['tv'];
        $nombreVia = $vector[$i]['dir']['nv'];

        $vias[] = $tipoVia . " - " . $nombreVia;
    }

    return $vias;
}
