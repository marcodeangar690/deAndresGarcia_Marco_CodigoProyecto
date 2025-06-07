<?php
require_once "conexion.php";
session_start();

//verificar que el usuario esta logueado y sino redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

//recuperacion de la orden del pedido y el carrito
$orderID = $_GET['orderID'] ?? '';
$cart    = $_SESSION['carrito'] ?? [];

if (empty($cart)) {
    echo "No hay compras para confirmar.";
    exit();
}

//guardado de cada entrada en la base de datos
$userId = $_SESSION['id_usuario'];
$stmt = $conexion->prepare("
    INSERT INTO compras 
      (id_usuario, id_evento, cantidad, order_id, fecha_compra)
    VALUES 
      (:user, :evento, :qty, :order, NOW())
");

foreach ($cart as $eventoId => $qty) {
    $stmt->execute([
        ':user'   => $userId,
        ':evento' => $eventoId,
        ':qty'    => $qty,
        ':order'  => $orderID
    ]);
}

//ver los detalles de las entradas compradas
$ids = array_keys($cart);
$ph  = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT * FROM eventos WHERE id_evento IN ($ph)";
$q   = $conexion->prepare($sql);
foreach ($ids as $i => $id) {
    $q->bindValue($i+1, $id, PDO::PARAM_INT);
}
$q->execute();
$items = $q->fetchAll(PDO::FETCH_ASSOC);

//calculo para el total de las entradas
$total = 0;
foreach ($items as $it) {
    $total += $it['precio'] * $cart[$it['id_evento']];
}

//vaciar carrito
unset($_SESSION['carrito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>¡Gracias por tu compra!</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-12">
  <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg text-center">
    <h1 class="text-3xl font-bold mb-4">¡Gracias por tu compra!</h1>
    <?php if ($orderID): ?>
      <p class="text-sm text-gray-600 mb-6">Order ID: <strong><?= htmlspecialchars($orderID) ?></strong></p>
    <?php endif; ?>

    <h2 class="text-xl font-semibold mb-2">Resumen de Entradas</h2>
    <ul class="divide-y divide-gray-200 mb-4 text-left">
      <?php foreach ($items as $it): ?>
        <?php $qty = $cart[$it['id_evento']]; ?>
        <li class="py-2 flex justify-between">
          <span><?= htmlspecialchars($it['nombre']) ?> x<?= $qty ?></span>
          <span><?= number_format($it['precio'] * $qty, 2) ?> €</span>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="text-right font-bold text-lg mb-6">
      Total pagado: <?= number_format($total, 2) ?> €
    </div>

    <a href="panel.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">
      Ir a Mi Panel
    </a>
  </div>
</body>
</html>
