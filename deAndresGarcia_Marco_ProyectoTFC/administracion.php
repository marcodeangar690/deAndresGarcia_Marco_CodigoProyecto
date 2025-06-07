<?php
require_once "conexion.php";
session_start();

//verificacion de que es el admin y sino redirigir a login
if (empty($_SESSION["is_admin"])) {
    header("Location: login.php");
    exit();
}

//parametros para la orden y la busqueda
$orden = $_GET["orden"] ?? "fecha_proxima";
$busq_evento = trim($_GET['busq_evento'] ?? '');
$busq_empresa = trim($_GET['busq_empresa'] ?? '');

$where = [];
$params = [];

if ($busq_evento !== '') {
    $where[] = "e.nombre LIKE :busq_evento";
    $params[':busq_evento'] = "%$busq_evento%";
}
if ($busq_empresa !== '') {
    $where[] = "emp.nombre_empresa LIKE :busq_empresa";
    $params[':busq_empresa'] = "%$busq_empresa%";
}
$whereSQL = '';
if ($where) {
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

//colocacion de las opciones del filtro
switch ($orden) {
    case "fecha_lejana":    $orderBy = "fecha DESC";      break;
    case "precio_bajo":     $orderBy = "precio ASC";       break;
    case "precio_alto":     $orderBy = "precio DESC";      break;
    case "creado_reciente": $orderBy = "id_evento DESC";   break;
    case "creado_antiguo":  $orderBy = "id_evento ASC";    break;
    default:                $orderBy = "fecha ASC";
}

//sentencia para los filtros y sacar todos los eventos
$sql = "
  SELECT
    e.*,
    emp.nombre_empresa AS empresa
  FROM eventos e
  LEFT JOIN empresas emp ON e.id_empresa = emp.id_empresa
  $whereSQL
  ORDER BY $orderBy
";
$stmt = $conexion->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-gray-100 to-gray-200 min-h-screen flex flex-col">

  <?php include 'includes/headerAdministracion.php'; ?>
  <?php
  if (!empty($_SESSION["mensaje"])): ?>
    <div id="success-message" class="container mx-auto px-4 py-4">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline"><?= htmlspecialchars($_SESSION["mensaje"]) ?></span>
      </div>
    </div>
  <?php
    unset($_SESSION["mensaje"]);
  endif;
  ?>
  <main class="container mx-auto px-4 py-10 flex-1">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
      <h1 class="text-4xl font-extrabold text-gray-800 flex items-center gap-2">
        <i class="ri-shield-user-line text-blue-600 text-5xl"></i> Administración de Eventos
      </h1>
      <div class="flex flex-col md:flex-row gap-4">
        <!-- filtros para la busqueda de los eventos -->
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
          <input type="text" name="busq_evento" value="<?=htmlspecialchars($busq_evento)?>" placeholder="Buscar evento..." class="px-3 py-2 border rounded focus:ring focus:ring-blue-300" />
          <input type="text" name="busq_empresa" value="<?=htmlspecialchars($busq_empresa)?>" placeholder="Buscar empresa..." class="px-3 py-2 border rounded focus:ring focus:ring-blue-300" />
          <select name="orden" onchange="this.form.submit()" class="px-3 py-2 border rounded focus:ring focus:ring-blue-300">
            <option value="fecha_proxima"   <?=$orden==='fecha_proxima'?'selected':''?>>Fecha próxima</option>
            <option value="fecha_lejana"     <?=$orden==='fecha_lejana'?'selected':''?>>Fecha lejana</option>
            <option value="precio_bajo"      <?=$orden==='precio_bajo'?'selected':''?>>Precio bajo</option>
            <option value="precio_alto"      <?=$orden==='precio_alto'?'selected':''?>>Precio alto</option>
            <option value="creado_reciente"  <?=$orden==='creado_reciente'?'selected':''?>>Creado reciente</option>
            <option value="creado_antiguo"   <?=$orden==='creado_antiguo'?'selected':''?>>Creado antiguo</option>
          </select>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
        </form>
        <a href="crear_evento.php" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-3 rounded-full shadow hover:bg-green-700 transition">
          <i class="ri-add-line text-xl"></i> Crear evento
        </a>
      </div>
    </div>

    <?php if (empty($eventos)): ?>
      <p class="text-center text-gray-600 text-lg">No hay eventos que coincidan.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach($eventos as $e): ?>
          <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition flex flex-col">
            <?php if (!empty($e['imagen'])): ?>
              <img src="uploads_eventos/<?=htmlspecialchars($e['imagen'])?>" alt="<?=htmlspecialchars($e['nombre'])?>" class="w-full h-48 object-cover rounded-t-2xl">
            <?php else: ?>
              <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400 rounded-t-2xl">
                <i class="ri-image-line text-5xl"></i>
              </div>
            <?php endif; ?>
            <div class="p-6 flex-1 flex flex-col justify-between">
              <div>
                <h2 class="text-2xl font-bold text-gray-800 truncate"><?=htmlspecialchars($e['nombre'])?></h2>
                <p class="text-sm text-gray-500 italic">Empresa: <?=htmlspecialchars($e['empresa'] ?? '–')?></p>
                <ul class="text-gray-600 text-sm space-y-1 mt-4">
                  <li><i class="ri-calendar-line text-blue-500 mr-1"></i><?=date("Y-m-d",strtotime($e['fecha']))?> @ <?=htmlspecialchars($e['hora'])?></li>
                  <li><i class="ri-map-pin-line text-blue-500 mr-1"></i><?=htmlspecialchars($e['ubicacion'])?></li>
                  <li><i class="ri-ticket-line text-blue-500 mr-1"></i><?=htmlspecialchars($e['precio'])?> €</li>
                </ul>
              </div>
              <div class="flex justify-between items-center pt-4 border-t">
                <button onclick="openModal(<?=$e['id_evento']?>)" class="flex items-center gap-1 text-blue-600 hover:underline">
                  <i class="ri-information-line"></i><span>Ver más</span>
                </button>
                <a href="eliminar_evento.php?id=<?=$e['id_evento']?>" class="flex items-center gap-1 text-red-600 hover:underline">
                  <i class="ri-delete-bin-line"></i><span>Eliminar</span>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- parte de modal para mostrar los detalles-->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl h-auto max-h-[80vh] overflow-y-auto p-6 relative">
      <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
        <i class="ri-close-line text-2xl"></i>
      </button>
      <div id="modal-content"></div>
    </div>
  </div>

  <script>
    function openModal(id) {
      const modal = document.getElementById('modal');
      const content = document.getElementById('modal-content');
      content.innerHTML = '<p class="text-center py-8">Cargando...</p>';
      modal.classList.remove('hidden');
      fetch(`detalle_evento.php?id=${id}&ajax=1`)
        .then(res => {
          if (!res.ok) throw new Error('Error cargando detalles');
          return res.text();
        })
        .then(html => content.innerHTML = html)
        .catch(err => content.innerHTML = `<p class="text-red-600">${err.message}</p>`);
    }
    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    //colocacion de los botones de ver mas a cada tarjeta de evento
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('button[data-id]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.getAttribute('data-id')));
      });
    });

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
