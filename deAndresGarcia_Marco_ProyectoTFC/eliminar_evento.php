<?php
require_once "conexion.php";
session_start();

//verificamos si es empresa o admin
$isAdmin = isset($_SESSION["id_rol"]) && $_SESSION["id_rol"] == 1;
if (!isset($_SESSION["id_empresa"]) && !$isAdmin) {
    header("Location: login_empresa.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: panel_empresa.php");
    exit();
}

$id_evento  = (int) $_GET["id"];
$id_empresa = $_SESSION["id_empresa"] ?? null;

//recuperacion de la info del evento
if ($isAdmin) {
    $sql = "SELECT * FROM eventos WHERE id_evento = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":id", $id_evento, PDO::PARAM_INT);
} else {
    $sql = "SELECT * FROM eventos WHERE id_evento = :id AND id_empresa = :empresa";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":id",      $id_evento, PDO::PARAM_INT);
    $stmt->bindParam(":empresa", $id_empresa, PDO::PARAM_INT);
}
$stmt->execute();
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    $_SESSION["error"] = "Evento no encontrado o no autorizado.";
    header("Location: panel_empresa.php");
    exit();
}

//confirmar eliminacion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($isAdmin) {
        $sql = "DELETE FROM eventos WHERE id_evento = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":id", $id_evento, PDO::PARAM_INT);
    } else {
        $sql = "DELETE FROM eventos WHERE id_evento = :id AND id_empresa = :empresa";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":id",      $id_evento, PDO::PARAM_INT);
        $stmt->bindParam(":empresa", $id_empresa, PDO::PARAM_INT);
    }
    $stmt->execute();

    $_SESSION["mensaje"] = "Evento eliminado correctamente.";
    //redireccion
    if ($isAdmin) {
        header("Location: administracion.php");
    } else {
        header("Location: panel_empresa.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar Eliminación</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { display: flex; flex-direction: column; min-height: 100vh; }
    main { flex-grow: 1; }
  </style>
</head>
<body class="bg-gradient-to-r from-red-100 to-pink-100">

  <?php include 'includes/headerContacto.php'; ?>

  <main class="flex items-center justify-center py-10 px-4">
    <div class="bg-white max-w-xl w-full p-6 sm:p-8 rounded-xl shadow-2xl">
      <h2 class="text-2xl sm:text-3xl font-bold text-red-600 mb-4 text-center">¿Estás seguro de que quieres eliminar este evento?</h2>

      <div class="mb-6 text-gray-700 space-y-2">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($evento["nombre"]) ?></p>
        <p class="text-sm sm:text-base"><strong>Fecha:</strong> <?= htmlspecialchars($evento["fecha"]) ?> a las <?= htmlspecialchars($evento["hora"]) ?></p>
        <p class="text-sm sm:text-base"><strong>Ubicación:</strong> <?= htmlspecialchars($evento["ubicacion"]) ?></p>
        <p class="text-sm sm:text-base"><strong>Precio:</strong> <?= htmlspecialchars($evento["precio"]) ?> €</p>
      </div>

      <form method="POST" class="flex flex-col sm:flex-row justify-between gap-4">
        <a href="<?= $isAdmin ? 'administracion.php' : 'panel_empresa.php' ?>" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-center">Cancelar</a>
        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-center">Sí, eliminar</button>
      </form>
    </div>
  </main>

  <?php include 'includes/footerContacto.php'; ?>

</body>
</html>
