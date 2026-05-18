<?php
require_once "../../models/vendor/econea/nusoap/src/nusoap.php";

header('Content-Type: application/json; charset=utf-8');

$url = "https://ovc.catastro.meh.es/ovcservweb/ovcswlocalizacionrc/ovccallejero.asmx?WSDL";

$cliente = new nusoap_client($url, true);
$cliente->soap_defencoding = "UTF-8";
$cliente->decode_utf8 = false;
$cliente->curl_options = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_TIMEOUT => 15,
];

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

function obtenerProvincias($cliente) {
    $respuesta = $cliente->call("ObtenerProvincias", "");
    if ($cliente->fault || !isset($respuesta['consulta_provinciero']['provinciero']['prov'])) {
        return [];
    }
    $vector = $respuesta['consulta_provinciero']['provinciero']['prov'];
    if (!is_array($vector)) return [];
    $provincias = [];
    foreach ($vector as $p) {
        if (isset($p['np'])) $provincias[] = $p['np'];
    }
    return $provincias;
}

function obtenerMunicipios($cliente, $provincia) {
    $respuesta = $cliente->call("ObtenerMunicipios", ["Provincia" => $provincia, "Municipio" => ""]);
    if ($cliente->fault || !isset($respuesta['consulta_municipiero']['municipiero']['muni'])) {
        return [];
    }
    $vector = $respuesta['consulta_municipiero']['municipiero']['muni'];
    if (!is_array($vector)) return [];
    $municipios = [];
    foreach ($vector as $m) {
        if (isset($m['nm'])) $municipios[] = $m['nm'];
    }
    return $municipios;
}

function obtenerVias($cliente, $provincia, $municipio) {
    $respuesta = $cliente->call("ObtenerCallejero", [
        "Provincia" => $provincia,
        "Municipio" => $municipio,
        "TipoVia"   => "",
        "NombresVia"=> ""
    ]);
    if ($cliente->fault || !isset($respuesta['consulta_callejero']['callejero']['calle'])) {
        return [];
    }
    $vector = $respuesta['consulta_callejero']['callejero']['calle'];
    if (!is_array($vector)) return [];
    $vias = [];
    foreach ($vector as $v) {
        if (isset($v['dir']['tv'], $v['dir']['nv'])) {
            $vias[] = $v['dir']['tv'] . " - " . $v['dir']['nv'];
        }
    }
    return $vias;
}