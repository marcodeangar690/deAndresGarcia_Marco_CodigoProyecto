<?php
require_once "conexion.php";
session_start();

//verificar que la empresa esta logueada y sino redirigir al login
if (!isset($_SESSION["id_empresa"])) {
    header("Location: login_empresa.php");
    exit();
}

$id_empresa = $_SESSION["id_empresa"];
$orden      = $_GET["orden"] ?? "fecha_proxima";

//filtro para los eventos en el panel
switch ($orden) {
    case "fecha_lejana":
        $orderBy = "fecha DESC";       break;
    case "precio_bajo":
        $orderBy = "precio ASC";       break;
    case "precio_alto":
        $orderBy = "precio DESC";      break;
    case "creado_reciente":
        $orderBy = "id_evento DESC";   break;
    case "creado_antiguo":
        $orderBy = "id_evento ASC";    break;
    default:
        $orderBy = "fecha ASC";        break;
}

$sql  = "SELECT * FROM eventos WHERE id_empresa = :id_empresa ORDER BY $orderBy";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id_empresa", $id_empresa, PDO::PARAM_INT);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Empresa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100">

<?php include 'includes/headerContacto.php'; ?>

<?php if (!empty($_SESSION["mensaje"])): ?>
  <div id="success-message" class="w-full px-4 lg:px-0">
    <div class="container mx-auto py-4">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= htmlspecialchars($_SESSION["mensaje"]) ?></span>
      </div>
    </div>
  </div>
<?php unset($_SESSION["mensaje"]); endif; ?>

<main class="flex-1 w-full px-4 lg:px-0">
  <div class="container mx-auto py-10">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-10">
      <h1 class="text-4xl font-extrabold text-gray-800 flex items-center gap-2">
        <i class="ri-calendar-event-line text-blue-600 text-5xl"></i>
        Mis Eventos
      </h1>
      <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
          <label for="orden" class="sr-only">Ordenar por</label>
          <select name="orden" id="orden" onchange="this.form.submit()" class="px-4 py-2 rounded-md border border-gray-300 focus:ring focus:ring-blue-300">
            <option value="fecha_proxima" <?= $orden === "fecha_proxima" ? "selected" : "" ?>>Fecha más próxima</option>
            <option value="fecha_lejana"   <?= $orden === "fecha_lejana"   ? "selected" : "" ?>>Fecha más lejana</option>
            <option value="precio_bajo"    <?= $orden === "precio_bajo"    ? "selected" : "" ?>>Precio más bajo</option>
            <option value="precio_alto"    <?= $orden === "precio_alto"    ? "selected" : "" ?>>Precio más alto</option>
            <option value="creado_reciente"<?= $orden === "creado_reciente"? "selected" : "" ?>>Más reciente creado</option>
            <option value="creado_antiguo" <?= $orden === "creado_antiguo" ? "selected" : "" ?>>Más antiguo creado</option>
          </select>
        </form>
        <a href="crear_evento.php" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-full shadow-md hover:bg-blue-700 transition">
          <i class="ri-add-line text-xl"></i> Añadir Evento
        </a>
      </div>
    </div>

    <?php if (count($eventos) === 0): ?>
      <p class="text-gray-600 text-center text-lg">No se encontraron eventos.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($eventos as $evento): ?>
          <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300">
            <?php if (!empty($evento["imagen"])): ?>
              <img src="uploads_eventos/<?= htmlspecialchars($evento["imagen"]) ?>" alt="<?= htmlspecialchars($evento["nombre"]) ?>" class="w-full h-48 object-cover">
            <?php else: ?>
              <div class="w-full h-48 bg-gradient-to-r from-blue-200 to-purple-200 flex items-center justify-center text-gray-400">
                <i class="ri-image-line text-5xl"></i>
              </div>
            <?php endif; ?>
            <div class="p-6">
              <h2 class="text-2xl font-bold text-gray-800 mb-2 truncate"><?= htmlspecialchars($evento["nombre"]) ?></h2>
              <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?= strip_tags($evento["descripcion"]) ?></p>

              <ul class="text-sm text-gray-600 space-y-1 mb-4">
                <li><i class="ri-calendar-line text-blue-500 mr-2"></i>
                  <strong>Fecha:</strong> <?= date("Y-m-d", strtotime($evento["fecha"])) ?> a las <?= htmlspecialchars($evento["hora"]) ?></li>
                <li><i class="ri-map-pin-line text-blue-500 mr-2"></i>
                  <strong>Ubicación:</strong> <?= htmlspecialchars($evento["ubicacion"]) ?></li>
                <li><i class="ri-ticket-line text-blue-500 mr-2"></i>
                  <strong>Precio:</strong> <?= htmlspecialchars($evento["precio"]) ?> €</li>
                <li><i class="ri-user-3-line text-blue-500 mr-2"></i>
                  <strong>Aforo:</strong> <?= htmlspecialchars($evento["aforo"]) ?></li>
                <li><i class="ri-price-tag-line text-blue-500 mr-2"></i>
                  <strong>Categoría:</strong> <?= htmlspecialchars($evento["categoria"]) ?></li>
              </ul>

              <div class="flex justify-between items-center pt-2 border-t mt-4">
                <a href="editar_evento.php?id=<?= $evento["id_evento"] ?>" class="text-blue-600 hover:underline flex items-center gap-1">
                  <i class="ri-edit-line"></i> Editar
                </a>
                <a href="eliminar_evento.php?id=<?= $evento["id_evento"] ?>" class="text-red-600 hover:underline flex items-center gap-1">
                  <i class="ri-delete-bin-line"></i> Eliminar
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>
<script>
  //desaparecer el mensaje a los 5 segundos
  setTimeout(() => {
    const msg = document.getElementById('success-message');
    if (msg) {
      msg.style.transition = 'opacity 0.5s ease';
      msg.style.opacity = '0';
      setTimeout(() => msg.remove(), 500);
    }
  }, 5000);
</script>

<?php include 'includes/footerContacto.php'; ?>

</body>
</html>
