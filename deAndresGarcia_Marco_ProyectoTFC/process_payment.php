<?php
session_start();
require_once "conexion.php";

//verificar que el usuario esta logueado, si no redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

//redirigir si no hay carrito
if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit();
}

//recuperar el carrito
$cart = $_SESSION['carrito'];
$ids  = array_keys($cart);

//recuperar los datos de los eventos
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT * FROM eventos WHERE id_evento IN ($placeholders)";
$stmt = $conexion->prepare($sql);
foreach ($ids as $i => $id) {
    $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
}
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

//generar una orden por pedido
$orderId = uniqid('ORD');

//fecha actual para la compra
$fechaCompra = date('Y-m-d H:i:s');

//añadir los elementos a la tabla de compras
$insert = $conexion->prepare("
    INSERT INTO compras
      (id_usuario, id_evento, cantidad, order_id, fecha_compra)
    VALUES
      (:uid, :eid, :qty, :oid, :fecha)
");
foreach ($items as $item) {
    $insert->execute([
        ':uid'   => $_SESSION['id_usuario'],
        ':eid'   => $item['id_evento'],
        ':qty'   => $cart[$item['id_evento']],
        ':oid'   => $orderId,
        ':fecha' => $fechaCompra
    ]);
}

//vaciar el carrito
unset($_SESSION['carrito']);

//simulación del metodo de pago
$method = $_POST['method'] ?? 'desconocido';
$methodName = $method === 'tarjeta' ? 'Tarjeta de crédito' : 'PayPal';
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Pago Confirmado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 text-center w-full max-w-lg">
      <h1 class="text-2xl sm:text-3xl font-bold mb-4 text-green-600">¡Pago Exitoso!</h1>
      <p class="mb-2 text-sm sm:text-base">Order ID: <strong><?= $orderId ?></strong></p>
      <p class="mb-4 text-sm sm:text-base">Has pagado con <strong><?= htmlspecialchars($methodName) ?></strong>.</p>
      <a href="panel.php" class="inline-block bg-blue-600 text-white px-5 py-2 sm:px-6 sm:py-3 rounded-full hover:bg-blue-700 transition text-sm sm:text-base">Ir a Mi Panel</a>
    </div>
  </body>
</html>
