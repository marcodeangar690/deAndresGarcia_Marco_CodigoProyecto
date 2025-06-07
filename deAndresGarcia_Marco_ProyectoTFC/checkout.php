<?php
require_once "conexion.php";
session_start();

//verificar que el usuario esta logueado, si no redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

//redireccion al carrito
if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit();
}

//sacar el contenido de cada entrada del carrito
$cart = $_SESSION['carrito'];
$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT * FROM eventos WHERE id_evento IN ($placeholders)";
$stmt = $conexion->prepare($sql);
foreach ($ids as $i => $id) {
    $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
}
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($items as $item) {
    $qty = $cart[$item['id_evento']];
    $total += $item['precio'] * $qty;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen" onload="AOS.init({ once: true });">

<?php include 'includes/headerContacto.php'; ?>

<main class="flex-grow container mx-auto py-10 px-4">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-8" data-aos="fade-down">Resumen de tu Compra</h1>

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-up">
        <?php foreach ($items as $item): 
            $qty = $cart[$item['id_evento']];?>
            <div class="flex justify-between border-b py-2">
                <div>
                    <h2 class="font-semibold"><?=htmlspecialchars($item['nombre'])?></h2>
                    <p class="text-sm text-gray-500">Cantidad: <?=$qty?></p>
                </div>
                <div class="font-semibold">
                    <?=number_format($item['precio'] * $qty,2)?> €
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-between text-xl font-bold mt-4">
            <span>Total:</span>
            <span><?=number_format($total,2)?> €</span>
        </div>
    </div>

    <form action="process_payment.php" method="POST" class="bg-white rounded-2xl shadow-lg p-6" data-aos="fade-up">
        <h3 class="text-2xl font-semibold mb-4">Método de pago</h3>

        <div class="space-y-4">
            <label class="flex items-center">
                <input type="radio" name="method" value="paypal" checked class="form-radio h-5 w-5 text-blue-600">
                <span class="ml-2">PayPal</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="method" value="tarjeta" class="form-radio h-5 w-5 text-blue-600">
                <span class="ml-2">Tarjeta de crédito</span>
            </label>
        </div>

        <button type="submit" class="mt-6 w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-2xl text-xl font-bold hover:opacity-90 transition">Pagar Ahora</button>
    </form>
</main>

<?php include 'includes/footerContacto.php'; ?>
</body>
</html>
