<?php
//iniciar la sesion
session_start();
header('Content-Type: application/json');
//verificar que hay un usuario logueado para poder continuar
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'No logueado']);
    exit;
}
//mandar el id del evento y la cantidad de entradas de ese evento
$id  = intval($_POST['id_evento']  ?? 0);
$qty = intval($_POST['quantity']   ?? 1);

if (!$id) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}
//iniciar el carrito si todavia no hay contenido dentro de este
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

//guardar la cantidad de entradas o para eliminar el evento del carrito
if ($qty >= 1 && $qty <= 5) {
    $_SESSION['carrito'][$id] = $qty;
} else {
    unset($_SESSION['carrito'][$id]);
}

//calculo para el total de entradas
$totalItems = array_sum($_SESSION['carrito']);

//mandar el resultado final
echo json_encode([
    'success' => true,
    'count'   => $totalItems
]);
