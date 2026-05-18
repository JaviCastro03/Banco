<?php
session_start();
if (!isset($_SESSION["idU"])) { http_response_code(403); exit; }

require_once "../models/ConexionDB.php";
require_once "../models/UsuarioDDBB.php";
require_once "../models/vendor/autoload.php";

$id = intval($_GET["id"] ?? 0);
$conexion = new UsuarioDDBB();
$t = $conexion->getTransaccionParaPDF($id, $_SESSION["idU"]);
if (!$t) { http_response_code(404); echo "Transacción no encontrada."; exit; }

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 20,
    'margin_bottom' => 20,
    'margin_left' => 20,
    'margin_right' => 20,
]);

$fecha = date('d/m/Y H:i', strtotime($t['fecha']));
$importe = number_format($t['importe'], 2, ',', '.');
$tipo = ucfirst($t['tipo']);
$estado = ucfirst($t['estado']);
$referencia = htmlspecialchars($t['referencia'] ?? '-');
$destinatario = htmlspecialchars($t['destinatario'] ?? '-');
$destino_ref = htmlspecialchars($t['destino_ref'] ?? '-');
$titular_origen = htmlspecialchars($t['titular_origen'] ?? '-');
$iban_origen = htmlspecialchars($t['iban_origen'] ?? '-');
$concepto = htmlspecialchars($t['concepto'] ?? '-');

$colorTipo = ($t['tipo'] === 'bizum') ? '#2d6a4f' : '#1a3a2a';
$iconoTipo = ($t['tipo'] === 'bizum') ? '⚡' : '🏦';

$html = "
<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<style>
  body { font-family: Arial, sans-serif; color: #1c2b22; margin: 0; padding: 0; }
  .header { background: #1a3a2a; color: #fff; padding: 28px 30px; border-radius: 10px 10px 0 0; }
  .header h1 { margin: 0; font-size: 22px; letter-spacing: 1px; }
  .header p { margin: 6px 0 0; font-size: 13px; color: #74c69d; }
  .badge { display: inline-block; background: #2d6a4f; color: #fff; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-top: 10px; }
  .card { border: 1px solid #e4ede7; border-radius: 0 0 10px 10px; padding: 30px; }
  .amount-box { text-align: center; background: #f0f7f4; border-radius: 10px; padding: 22px; margin: 20px 0; }
  .amount-box .label { font-size: 12px; color: #6b7c72; text-transform: uppercase; letter-spacing: 1px; }
  .amount-box .amount { font-size: 38px; font-weight: bold; color: #1a3a2a; }
  .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  .info-table tr { border-bottom: 1px solid #e4ede7; }
  .info-table tr:last-child { border-bottom: none; }
  .info-table td { padding: 10px 6px; font-size: 13px; }
  .info-table td:first-child { color: #6b7c72; width: 45%; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
  .info-table td:last-child { color: #1c2b22; font-weight: 500; }
  .estado-ok { color: #2d6a4f; font-weight: bold; }
  .footer { text-align: center; margin-top: 28px; font-size: 11px; color: #aab7b0; border-top: 1px solid #e4ede7; padding-top: 16px; }
  .ref-box { background: #f6f4ef; border-radius: 8px; padding: 12px 16px; margin-top: 16px; font-family: monospace; font-size: 13px; color: #1a3a2a; text-align: center; letter-spacing: 1px; }
</style>
</head>
<body>
  <div class='header'>
    <h1>EcoBank — Comprobante de {$tipo}</h1>
    <p>Documento generado el " . date('d/m/Y') . " a las " . date('H:i') . "</p>
    <span class='badge'>{$tipo}</span>
  </div>
  <div class='card'>
    <div class='amount-box'>
      <div class='label'>Importe de la operación</div>
      <div class='amount'>€{$importe}</div>
    </div>
    <table class='info-table'>
      <tr><td>Estado</td><td><span class='estado-ok'>{$estado}</span></td></tr>
      <tr><td>Fecha</td><td>{$fecha}</td></tr>
      <tr><td>Titular origen</td><td>{$titular_origen}</td></tr>
      <tr><td>IBAN origen</td><td>{$iban_origen}</td></tr>
      <tr><td>Destinatario</td><td>{$destinatario}</td></tr>
      <tr><td>" . ($t['tipo'] === 'bizum' ? 'Teléfono destino' : 'IBAN destino') . "</td><td>{$destino_ref}</td></tr>
      <tr><td>Concepto</td><td>{$concepto}</td></tr>
    </table>
    <div class='footer'>
      EcoBank — Banca Sostenible · Este documento es un comprobante oficial de la operación realizada.
      <br>Para cualquier consulta contacta con soporte@ecobank.es
    </div>
  </div>
</body>
</html>
";

$mpdf->WriteHTML($html);
$mpdf->Output("comprobante_{$referencia}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
