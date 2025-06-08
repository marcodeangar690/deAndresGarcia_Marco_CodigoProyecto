<?php
require_once "conexion.php";
session_start();

//verificar logueo del usuario, si no esta redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

//normalizacion del carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
} else {
    $raw = $_SESSION['carrito'];
    $normalized = [];
    foreach ($raw as $key => $val) {
        $k = intval($key);
        $v = intval($val);
        //ver si el valor es mayor a 5
        if ($v > 5) {
            $normalized[$k] = 1;
        }
        //para cuando sea mayor de 5 la cantidad de entradas
        elseif ($k > 5) {
            $normalized[$k] = max(1, min($v, 5));
        }
        ///para cuando sea menor o igual de 5 la cantidad de entradas
        else {
            $normalized[$k] = max(1, min($v, 5));
        }
    }
    $_SESSION['carrito'] = $normalized;
}

//para eliminar evento del carrito
if (isset($_GET['remove'])) {
    $removeId = intval($_GET['remove']);
    unset($_SESSION['carrito'][$removeId]);
    header('Location: carrito.php');
    exit();
}

//para actualizar cantidad de entradas en el carrito
if (isset($_POST['update_qty'])) {
    $eventId = intval($_POST['event_id']);
    $qty = intval($_POST['quantity']);
    if ($qty >= 1 && $qty <= 5) {
        $_SESSION['carrito'][$eventId] = $qty;
    } else {
        unset($_SESSION['carrito'][$eventId]);
    }
    header('Location: carrito.php');
    exit();
}

$items = [];
$total = 0;
$cart = $_SESSION['carrito'];

if (!empty($cart)) {
    //recuperacion de los datos de los eventos en el carrito
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT * FROM eventos WHERE id_evento IN ($placeholders)";
    $stmt = $conexion->prepare($sql);
    foreach ($ids as $idx => $id) {
        $stmt->bindValue($idx + 1, $id, PDO::PARAM_INT);
    }
    $stmt->execute();
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //para ordenar
    $byId = [];
    foreach ($fetched as $row) {
        $byId[$row['id_evento']] = $row;
    }
    foreach ($ids as $id) {
        if (isset($byId[$id])) {
            $items[] = $byId[$id];
        }
    }

    //calcular el total de las entradas
    foreach ($items as $item) {
        $qty = $cart[$item['id_evento']];
        $total += $item['precio'] * $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu Carrito</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen" onload="AOS.init({ once: true });">

<?php include 'includes/headerCarrito.php'; ?>

<main class="flex-grow w-full max-w-5xl mx-auto py-10 px-4 sm:px-6">
    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 mb-8 flex items-center gap-2" data-aos="fade-down"><i class="ri-shopping-cart-fill text-blue-600 text-4xl sm:text-5xl"></i> Tu Carrito</h1>

    <?php if (empty($items)): ?>
        <div class="text-center py-20" data-aos="fade-up">
            <p class="text-lg sm:text-xl text-gray-600 mb-4">Tu carrito está vacío.</p>
            <a href="eventos.php" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition text-sm sm:text-base">Ver Eventos</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" data-aos="fade-up">
            <!-- lista de entradas del carrito -->
            <div class="lg:col-span-2 space-y-6">
                <?php foreach ($items as $item): 
                    $qty = $cart[$item['id_evento']];?>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col sm:flex-row items-stretch hover:shadow-2xl transition" data-aos="zoom-in">
                        <!-- para ajustar el tamaño de las imagenes en la entrada -->
                        <img src="uploads_eventos/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" class="w-full sm:w-48 h-50 sm:h-50 object-cover">
                        <div class="p-4 sm:p-6 flex-grow flex flex-col justify-between min-w-0">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-2 truncate"><?= htmlspecialchars($item['nombre']) ?></h2>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1 flex items-center gap-1">
                                    <i class="ri-map-pin-line text-blue-500"></i>
                                    <?= htmlspecialchars($item['ubicacion']) ?>
                                </p>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1 flex items-center gap-1">
                                    <i class="ri-price-tag-line text-blue-500"></i>
                                    <?= htmlspecialchars($item['categoria']) ?>
                                </p>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1 flex items-center gap-1">
                                    <i class="ri-calendar-line text-blue-500"></i>
                                    <?= date('Y-m-d', strtotime($item['fecha'])) ?> 
                                    <?= htmlspecialchars($item['hora']) ?>
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                                <form method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="update_qty" value="1">
                                    <input type="hidden" name="event_id" value="<?= $item['id_evento'] ?>">
                                    <label for="qty_<?= $item['id_evento'] ?>" class="text-sm sm:text-base">Cantidad:</label>
                                    <select id="qty_<?= $item['id_evento'] ?>" name="quantity" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm sm:text-base">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i === $qty ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                                <div class="flex items-center gap-4">
                                    <span class="text-lg sm:text-xl font-bold text-gray-800">
                                        <?= number_format($item['precio'] * $qty, 2) ?> €
                                    </span>
                                    <a href="carrito.php?remove=<?= $item['id_evento'] ?>"
                                       class="text-red-600 hover:text-red-800 flex items-center gap-1 text-sm sm:text-base">
                                        <i class="ri-delete-bin-line"></i> Quitar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- parte del resumen de las entradas y checkout -->
            <aside class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 flex flex-col justify-between h-full" data-aos="fade-left">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">Resumen</h3>
                    <div class="space-y-2 text-sm sm:text-base">
                        <?php foreach ($items as $item): 
                            $qty = $cart[$item['id_evento']];?>
                            <div class="flex justify-between">
                                <span><?= htmlspecialchars($item['nombre']) ?> x<?= $qty ?></span>
                                <span><?= number_format($item['precio'] * $qty, 2) ?> €</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="flex justify-between text-lg sm:text-xl font-semibold mb-4">
                        <span>Total:</span>
                        <span><?= number_format($total, 2) ?> €</span>
                    </div>
                    <a href="checkout.php" class="block text-center bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 sm:py-4 rounded-2xl text-lg sm:text-xl font-bold hover:opacity-90 transition">
                        Proceder al Pago
                    </a>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footerContacto.php'; ?>

</body>
</html>
