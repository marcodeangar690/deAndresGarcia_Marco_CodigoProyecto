<?php
session_start();
require_once "conexion.php";
require_once __DIR__ . '/phpqrcode/qrlib.php';

//verificar que esta el usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    exit('No autorizado');
}
$uid = $_SESSION['id_usuario'];

//validacion de los parametros
if (!isset($_GET['order'], $_GET['id'])) {
    http_response_code(400);
    exit('Parámetros inválidos');
}
$orderId  = $_GET['order'];
$eventoId = intval($_GET['id']);

//lectura de la compra de la entrada en la base de datos
$stmt = $conexion->prepare("
    SELECT c.cantidad, c.fecha_compra, e.nombre
    FROM compras c
    JOIN eventos  e ON c.id_evento = e.id_evento
    WHERE c.order_id   = ?
      AND c.id_evento  = ?
      AND c.id_usuario = ?
    LIMIT 1
");
$stmt->execute([$orderId, $eventoId, $uid]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    http_response_code(404);
    exit('No encontrado');
}

//preparar el texto que va en la entrada
$txt  = "Order: {$orderId}\n";
$txt .= "Usuario: {$uid}\n";
$txt .= "Evento: {$data['nombre']} ({$eventoId})\n";
$txt .= "Cantidad: {$data['cantidad']}\n";
$txt .= "Fecha: {$data['fecha_compra']}";

//generacion del qr
header('Content-Type: image/png');
QRcode::png($txt, false, QR_ECLEVEL_M, 6);