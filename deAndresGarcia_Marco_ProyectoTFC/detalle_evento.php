<?php
require_once "conexion.php";

//verificar el id del evento que se quiere ver
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit("Evento no válido.");
}
$id = (int)$_GET['id'];

//recuperar los datos del evento para despues mostrarlos
$stmt = $conexion->prepare("SELECT * FROM eventos WHERE id_evento = :id");
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ev) {
    http_response_code(404);
    exit("Evento no encontrado.");
}

//devolucion unicamente del html
if (isset($_GET['ajax'])):
?>
  <div class="space-y-4 px-4 lg:px-0">
    <h2 class="text-2xl font-bold"><?= htmlspecialchars($ev['nombre']) ?></h2>

    <?php if ($ev['imagen']): ?>
      <img src="uploads_eventos/<?= htmlspecialchars($ev['imagen']) ?>" alt="<?= htmlspecialchars($ev['nombre']) ?>" class="w-full rounded-lg shadow-lg mb-6 object-cover h-auto">
    <?php endif; ?>

    <div class="prose max-w-none">
      <?= $ev['descripcion'] ?>
    </div>

    <ul class="text-sm text-gray-700 space-y-1 pt-4 border-t">
      <li><i class="ri-calendar-line text-blue-500"></i>
        <strong>Fecha:</strong>
        <?= date('Y-m-d', strtotime($ev['fecha'])) ?> a las <?= htmlspecialchars($ev['hora']) ?></li>
      <li><i class="ri-map-pin-line text-blue-500"></i>
        <strong>Ubicación:</strong> <?= htmlspecialchars($ev['ubicacion']) ?></li>
      <li><i class="ri-ticket-line text-blue-500"></i>
        <strong>Precio:</strong> <?= htmlspecialchars($ev['precio']) ?> €</li>
      <li><i class="ri-user-3-line text-blue-500"></i>
        <strong>Aforo:</strong> <?= htmlspecialchars($ev['aforo']) ?></li>
    </ul>
  </div>
<?php
  exit;
endif;
?>  
