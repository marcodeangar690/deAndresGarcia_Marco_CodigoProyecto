<?php
session_start();
require_once "conexion.php";

//verificar que el usuario esta logueado si no redirigir a login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['id_usuario'];

//recuperar todas las compras del usuario
$stmt = $conexion->prepare("SELECT DISTINCT order_id, fecha_compra FROM compras WHERE id_usuario = ? ORDER BY fecha_compra DESC");
$stmt->execute([$uid]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//funcion para obtener los elementos de cada pedido
function getItemsByOrder($conexion, $orderId) {
    $sql = "SELECT c.id_evento, c.cantidad, e.nombre, e.imagen, e.precio FROM compras c JOIN eventos e ON c.id_evento = e.id_evento WHERE c.order_id = ?";
    $q = $conexion->prepare($sql);
    $q->execute([$orderId]);
    return $q->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title>Mi Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
      rel="icon"
      href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  </head>
  <body class="bg-gray-100 flex flex-col min-h-screen">

    <?php include 'includes/headerContacto.php'; ?>

    <main class="flex-grow w-full max-w-5xl mx-auto py-10 px-4" x-data>
      <h1 class="text-3xl sm:text-4xl font-extrabold mb-8 text-gray-800">🛍️ Mis Compras</h1>

      <?php if (empty($pedidos)): ?>
        <div class="text-center py-20">
          <p class="text-lg sm:text-xl text-gray-600 mb-4">
            Aún no has realizado ninguna compra.
          </p>
          <a href="eventos.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">
            Ver Eventos
          </a>
        </div>
      <?php else: ?>
        <div class="space-y-6">
          <?php foreach ($pedidos as $p): 
            $items     = getItemsByOrder($conexion, $p['order_id']);
            $countTotal = array_sum(array_column($items, 'cantidad'));
            $sumTotal   = array_sum(array_map(fn($i) => $i['precio'] * $i['cantidad'], $items));
          ?>
          <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <button
              @click="open = !open"
              class="w-full flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 sm:p-6 focus:outline-none">
              <div class="w-full sm:w-auto">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">
                  Pedido <?= htmlspecialchars($p['order_id']) ?> – 
                  <span class="text-sm text-gray-500"><?= htmlspecialchars($p['fecha_compra']) ?></span>
                </h2>
                <p class="mt-2 sm:mt-1 text-gray-500">
                  <span class="font-medium"><?= $countTotal ?></span> entrada<?= $countTotal > 1 ? 's' : '' ?> • 
                  <span class="font-medium"><?= number_format($sumTotal, 2) ?> €</span>
                </p>
              </div>
              <svg :class="open ? 'rotate-180' : ''"
                class="h-6 w-6 text-gray-500 transform transition-transform duration-200 mt-3 sm:mt-0"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <div x-show="open" x-collapse class="border-t border-gray-200 p-4 sm:p-6 bg-gray-50">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <?php foreach ($items as $it): 
                  for ($n = 1; $n <= $it['cantidad']; $n++): 
                ?>
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition flex flex-col">
                  <img src="uploads_eventos/<?= htmlspecialchars($it['imagen']) ?>"
                    alt="<?= htmlspecialchars($it['nombre']) ?>"
                    class="h-32 sm:h-40 w-full object-cover rounded-t-xl">
                  <div class="p-3 sm:p-4 flex-grow flex flex-col justify-between">
                    <div>
                      <h3 class="text-md sm:text-lg font-semibold text-gray-800 mb-1 truncate">
                        <?= htmlspecialchars($it['nombre']) ?>
                      </h3>
                      <p class="text-xs sm:text-sm text-gray-500 mb-1">
                        Entrada #<?= $n ?> de <?= $it['cantidad'] ?>
                      </p>
                      <p class="text-xs sm:text-sm text-gray-500">
                        <strong>Precio:</strong> <?= number_format($it['precio'], 2) ?> €
                      </p>
                    </div>
                    <div class="mt-3">
                      <a href="ticket.php?order=<?= urlencode($p['order_id']) ?>&id=<?= $it['id_evento'] ?>"
                        class="block bg-gradient-to-r from-blue-600 to-purple-600
                               text-white px-3 sm:px-4 py-2 rounded-lg text-center
                               hover:opacity-90 transition text-sm sm:text-base">
                        Ver Entrada
                      </a>
                    </div>
                  </div>
                </div>
                <?php endfor; endforeach; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>

    <?php include 'includes/footerContacto.php'; ?>

  </body>
</html>
