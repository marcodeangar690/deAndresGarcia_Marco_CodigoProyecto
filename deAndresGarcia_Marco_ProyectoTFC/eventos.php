<?php
require_once "conexion.php";
session_start();

//verificacion de usuario y empresa, si no redirigir a login
if (!isset($_SESSION["id_usuario"]) && !isset($_SESSION["id_empresa"])) {
    header("Location: login.php");
    exit();
}

//orden, busqueda y categoria
$orden     = $_GET['orden']     ?? 'fecha_proxima';
$busqueda  = trim($_GET['busqueda'] ?? '');
$view      = $_GET['view']      ?? 'grid';
$categoria = trim($_GET['categoria'] ?? '');

//cargar categorias
$catStmt    = $conexion->query("SELECT DISTINCT categoria FROM eventos ORDER BY categoria");
$categorias = $catStmt->fetchAll(PDO::FETCH_COLUMN);

//ordenar el filtro
switch ($orden) {
    case 'fecha_lejana': $orderBy = 'fecha DESC';  break;
    case 'precio_bajo':  $orderBy = 'precio ASC';   break;
    case 'precio_alto':  $orderBy = 'precio DESC';  break;
    default:             $orderBy = 'fecha ASC';    break;
}

//sentencia para los eventos
$sql = "
  SELECT 
    e.*,
    (e.aforo - COALESCE(SUM(c.cantidad), 0)) AS entradas_disponibles
  FROM eventos e
  LEFT JOIN compras c 
    ON c.id_evento = e.id_evento
  WHERE 
    (e.nombre LIKE :busqueda OR e.ubicacion LIKE :busqueda)
    AND (:categoria = '' OR e.categoria = :categoria)
  GROUP BY e.id_evento
  ORDER BY $orderBy
";
$stmt = $conexion->prepare($sql);
$like = "%{$busqueda}%";
$stmt->bindParam(':busqueda', $like);
$stmt->bindParam(':categoria', $categoria);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eventos Disponibles</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    body { display: flex; flex-direction: column; min-height: 100vh; }
    main { flex-grow: 1; }
    .calendar { display: grid; grid-template-columns: repeat(7,1fr); gap:1px; background:#ddd; }
    .day { background:#fff; padding:8px; min-height:80px; border:1px solid #ccc; }
    #toast { position:fixed; bottom:1rem; right:1rem; background:rgba(0,0,0,0.8); color:white; padding:1rem 1.5rem; border-radius:8px; opacity:0; transition:opacity .3s; }
  </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-100" onload="AOS.init({once:true});">

  <?php include 'includes/headerContacto.php'; ?>

  <div class="flex flex-1">
    <aside class="hidden lg:block w-72 sticky top-20 h-screen overflow-auto bg-white p-6 border-r">
      <h2 class="text-xl font-bold mb-4">Filtrar Eventos</h2>
      <div>
        <button class="w-full text-left font-semibold py-2 border-b" onclick="toggle('filtroFechaSidebar')">
          Fecha <i class="ri-arrow-down-s-line inline"></i>
        </button>
        <div id="filtroFechaSidebar" class="hidden py-2">
          <select id="ordenSidebar" onchange="applyFilter()" class="w-full border p-2 rounded">
            <option value="fecha_proxima" <?= $orden==='fecha_proxima' ? 'selected':'' ?>>Más próxima</option>
            <option value="fecha_lejana"   <?= $orden==='fecha_lejana'   ? 'selected':'' ?>>Más lejana</option>
          </select>
        </div>
      </div>
      <div class="mt-4">
        <button class="w-full text-left font-semibold py-2 border-b" onclick="toggle('filtroPrecioSidebar')">
          Precio <i class="ri-arrow-down-s-line inline"></i>
        </button>
        <div id="filtroPrecioSidebar" class="hidden py-2">
          <select id="precioOrdenSidebar" onchange="applyFilter()" class="w-full border p-2 rounded">
            <option value="precio_bajo" <?= $orden==='precio_bajo' ? 'selected':'' ?>>Más bajo</option>
            <option value="precio_alto" <?= $orden==='precio_alto' ? 'selected':'' ?>>Más alto</option>
          </select>
        </div>
      </div>
      <div class="mt-4">
        <button class="w-full text-left font-semibold py-2 border-b" onclick="toggle('filtroCategoriaSidebar')">
          Categoría <i class="ri-arrow-down-s-line inline"></i>
        </button>
        <div id="filtroCategoriaSidebar" class="hidden py-2">
          <select id="categoriaSidebar" onchange="applyFilter()" class="w-full border p-2 rounded">
            <option value="">Todas las categorías</option>
            <?php foreach($categorias as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>"
                <?= $cat === $categoria ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="mt-6">
        <button onclick="resetFilters()" class="bg-blue-600 text-white w-full py-2 rounded">Limpiar</button>
      </div>
    </aside>

    <main class="flex-grow mx-auto w-full max-w-7xl px-4 lg:px-6 py-8">
      <div class="block lg:hidden mb-6 space-y-4 bg-white p-4 rounded-lg shadow">
        <h2 class="text-lg font-semibold">Filtrar Eventos</h2>
        <div>
          <label class="block font-medium mb-1">Ordenar por Fecha:</label>
          <select id="ordenMobile" onchange="applyFilter()" class="w-full border p-2 rounded mb-2">
            <option value="fecha_proxima" <?= $orden==='fecha_proxima' ? 'selected':'' ?>>Más próxima</option>
            <option value="fecha_lejana"   <?= $orden==='fecha_lejana'   ? 'selected':'' ?>>Más lejana</option>
          </select>
        </div>
        <div>
          <label class="block font-medium mb-1">Ordenar por Precio:</label>
          <select id="precioOrdenMobile" onchange="applyFilter()" class="w-full border p-2 rounded mb-2">
            <option value="precio_bajo" <?= $orden==='precio_bajo' ? 'selected':'' ?>>Más bajo</option>
            <option value="precio_alto" <?= $orden==='precio_alto' ? 'selected':'' ?>>Más alto</option>
          </select>
        </div>
        <div>
          <label class="block font-medium mb-1">Categoría:</label>
          <select id="categoriaFiltroMobile" onchange="applyFilter()" class="w-full border p-2 rounded mb-2">
            <option value="">Todas las categorías</option>
            <?php foreach($categorias as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>"
                <?= $cat === $categoria ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button onclick="resetFilters()" class="bg-blue-600 text-white w-full py-2 rounded">Limpiar todo</button>
      </div>

      <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <h1 class="text-4xl font-extrabold text-gray-800">Eventos Disponibles</h1>
        <div class="flex gap-2 flex-wrap items-center">
          <input id="busqueda" type="text" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>" class="px-3 py-2 border rounded focus:ring focus:ring-blue-300 flex-grow" />

          <button onclick="applyFilter()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="ri-filter-line"></i> Filtrar
          </button>

          <button onclick="changeView('grid')" class="px-3 py-2 border rounded hover:bg-gray-200" title="Grid">
            <i class="ri-layout-grid-line"></i>
          </button>
          <button onclick="changeView('calendar')" class="px-3 py-2 border rounded hover:bg-gray-200" title="Calendario">
            <i class="ri-calendar-line"></i>
          </button>
        </div>
      </div>

      <div id="skeleton" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php for($i=0;$i<6;$i++): ?>
          <div class="animate-pulse bg-white rounded-2xl h-80"></div>
        <?php endfor; ?>
      </div>

      <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-aos="fade-up">
        <?php
          $hoy = date('Y-m-d');
          foreach($eventos as $evento):
            $fechaEvento = date('Y-m-d', strtotime($evento['fecha']));
            if ($fechaEvento < $hoy) {
              $status      = 'Evento no disponible';
              $statusClass = 'text-gray-500';
            }
            elseif ($evento['entradas_disponibles'] <= 0) {
              $status      = 'Sold Out';
              $statusClass = 'text-red-600';
            }
            elseif ($evento['entradas_disponibles'] < 10) {
              $status      = 'Quedan pocas entradas';
              $statusClass = 'text-yellow-600';
            }
            else {
              $status      = 'Quedan entradas';
              $statusClass = 'text-green-600';
            }
        ?>
          <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-transform transform hover:scale-105" data-aos="fade-up" data-aos-delay="50">
            <?php if($evento['imagen']): ?>
              <img src="uploads_eventos/<?= htmlspecialchars($evento['imagen']) ?>" class="w-full h-48 object-cover"/>
            <?php else: ?>
              <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                <i class="ri-image-line text-5xl text-gray-400"></i>
              </div>
            <?php endif; ?>

            <div class="p-6">
              <h2 class="text-2xl font-semibold text-gray-800 mb-2 truncate">
                <?= htmlspecialchars($evento['nombre']) ?>
              </h2>
              <p class="text-sm text-gray-600 mb-2 line-clamp-3">
                <?= strip_tags($evento['descripcion']) ?>
              </p>
              <p class="text-sm text-gray-500 mb-1">
                <i class="ri-calendar-line text-blue-500"></i>
                <?= date('Y-m-d', strtotime($evento['fecha'])) ?> |
                <?= htmlspecialchars($evento['hora']) ?>
              </p>
              <p class="text-sm text-gray-500 mb-1">
                <i class="ri-map-pin-line text-blue-500"></i>
                <?= htmlspecialchars($evento['ubicacion']) ?>
              </p>
              <p class="text-sm text-gray-500 mb-1">
                <i class="ri-price-tag-line text-blue-500"></i>
                <?= htmlspecialchars($evento['categoria']) ?>
              </p>
              <p class="text-sm text-gray-500 mb-4">
                <i class="ri-ticket-line text-blue-500"></i>
                <?= htmlspecialchars($evento['precio']) ?> €
              </p>

              <div class="flex justify-between items-center">
                <button type="button" onclick="openQuickView(<?= $evento['id_evento'] ?>)" class="text-blue-600 hover:underline flex items-center gap-1">
                  <i class="ri-information-line"></i> Info
                </button>

                <span class="text-sm font-medium <?= $statusClass ?>">
                  <i class="ri-ticket-line"></i> <?= $status ?>
                </span>

                <?php if ($fechaEvento >= $hoy && $evento['entradas_disponibles'] > 0): ?>
                  <button onclick="addToCart(<?= $evento['id_evento'] ?>)" class="bg-green-600 text-white px-3 py-1 rounded-full hover:bg-green-700 transition flex items-center gap-1">
                    <i class="ri-shopping-cart-fill"></i> Añadir
                  </button>
                <?php else: ?>
                  <button disabled class="bg-gray-300 text-gray-600 px-3 py-1 rounded-full flex items-center gap-1 cursor-not-allowed">
                    <i class="ri-shopping-cart-fill"></i> Añadir
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div id="calendarView" class="hidden" data-aos="fade-up">
        <?php
          $year        = date('Y');
          $month       = date('n');
          $monthName   = ucfirst(date('F', strtotime("$year-$month-01")));
          $firstWeekday = date('N', strtotime("$year-$month-01")); // 1 Lunes…7 Dom
          $daysInMonth  = date('t', strtotime("$year-$month-01"));

          //colocacion de eventos por dia
          $eventsByDay = [];
          foreach ($eventos as $e) {
            $d = date('j', strtotime($e['fecha']));
            $m = date('n', strtotime($e['fecha']));
            $y = date('Y', strtotime($e['fecha']));
            if ($m == $month && $y == $year) {
              $eventsByDay[$d][] = $e;
            }
          }
        ?>
        <h3 class="text-xl font-semibold mb-4 text-center"><?= htmlspecialchars("$monthName $year") ?></h3>
        <div class="calendar mb-6">
          <?php
            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
            foreach ($dias as $d) {
              echo "<div class='text-center font-bold bg-gray-200 p-2'>{$d}</div>";
            }
            for ($i = 1; $i < $firstWeekday; $i++) {
              echo "<div class='bg-gray-100'></div>";
            }
            for ($day = 1; $day <= $daysInMonth; $day++) {
              echo "<div class='day'><div class='font-semibold mb-1'>{$day}</div>";
              if (!empty($eventsByDay[$day])) {
                foreach ($eventsByDay[$day] as $ev) {
                  echo "<div class='text-xs truncate'><i class='ri-star-line text-yellow-500'></i> "
                     . htmlspecialchars($ev['nombre'])
                     . "</div>";
                }
              }
              echo "</div>";
            }
          ?>
        </div>
      </div>

    </main>
  </div>

  <?php include 'includes/footerContacto.php'; ?>

  <div id="quickModal" class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-black/70 to-black/50 hidden z-50">
    <div class="relative bg-white rounded-3xl p-10 w-full max-w-4xl shadow-2xl transform transition-transform duration-300 ease-out hover:scale-105" data-aos="flip-down">
      <button onclick="closeQuickView()" class="absolute top-5 right-5 text-gray-600 hover:text-gray-900 focus:outline-none">
        <i class="ri-close-line text-3xl"></i>
      </button>
      <div id="quickContent" class="overflow-auto max-h-[85vh] space-y-6 text-gray-800">
      </div>
    </div>
  </div>

  <div id="toast"></div>

  <script>
    AOS.init();

    function toggle(id) {
      document.getElementById(id).classList.toggle('hidden');
    }

    function applyFilter() {
      const busqueda     = document.getElementById('busqueda').value;
      const catMobile    = document.getElementById('categoriaFiltroMobile');
      const catSidebar   = document.getElementById('categoriaSidebar');
      const cat          = catSidebar ? catSidebar.value : (catMobile ? catMobile.value : '');
      const fechaSelect  = document.getElementById('ordenSidebar')   || document.getElementById('ordenMobile');
      const precioSelect = document.getElementById('precioOrdenSidebar') || document.getElementById('precioOrdenMobile');
      let ordenParam     = 'fecha_proxima';

      if (fechaSelect && fechaSelect.value) {
        ordenParam = fechaSelect.value;
      }
      if (precioSelect && precioSelect.value) {
        ordenParam = precioSelect.value;
      }

      const view = 'grid';
      const params = new URLSearchParams({
        busqueda:  busqueda.trim(),
        orden:     ordenParam,
        view:      view,
        categoria: cat
      });
      window.location.search = params.toString();
    }

    function resetFilters() {
      window.location.href = 'eventos.php';
    }

    function changeView(v) {
      document.getElementById('gridView').classList.toggle('hidden', v !== 'grid');
      document.getElementById('calendarView').classList.toggle('hidden', v !== 'calendar');
      document.getElementById('skeleton').classList.add('hidden');
    }

    function addToCart(id) {
      fetch('ajax_add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id_evento=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const span = document.getElementById('cartCount');
          if (span) span.textContent = data.count;
          showToast('Evento añadido al carrito');
        } else {
          showToast('Error: ' + (data.error||''));
        }
      })
      .catch(() => showToast('Error al comunicarse con el servidor'));
    }

    function showToast(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.style.opacity = 1;
      setTimeout(() => t.style.opacity = 0, 3000);
    }

    function openQuickView(id) {
      document.getElementById('quickModal').classList.remove('hidden');
      fetch(`detalle_evento.php?id=${id}&ajax=1`)
        .then(res => res.text())
        .then(html => {
          document.getElementById('quickContent').innerHTML = html;
        });
    }

    function closeQuickView() {
      document.getElementById('quickModal').classList.add('hidden');
    }

    //simulacion de carga
    setTimeout(() => {
      document.getElementById('skeleton').classList.add('hidden');
      document.getElementById('gridView').classList.remove('hidden');
    }, 800);
  </script>
</body>
</html>
