<?php
require_once "conexion.php";
session_start();
//verificar que la empresa esta logueada, si no redirigir a login
if (!isset($_SESSION["id_empresa"])) {
    header("Location: login_empresa.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: panel_empresa.php");
    exit();
}

$id_evento  = $_GET["id"];
$id_empresa = $_SESSION["id_empresa"];

//recuperar datos actuales del evento
$sql = "SELECT * FROM eventos WHERE id_evento = :id_evento AND id_empresa = :id_empresa";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id_evento", $id_evento, PDO::PARAM_INT);
$stmt->bindParam(":id_empresa", $id_empresa, PDO::PARAM_INT);
$stmt->execute();
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    $_SESSION["error"] = "Evento no encontrado.";
    header("Location: panel_empresa.php");
    exit();
}

//parametros a pasar para la actualizacion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST["nombre"]);
    $descripcion = $_POST["descripcion"];
    $fecha       = $_POST["fecha"];
    $hora        = $_POST["hora"];
    $precio      = floatval($_POST["precio"]);
    $ubicacion   = trim($_POST["ubicacion"]);
    $aforo       = intval($_POST["aforo"]);
    $categoria   = $_POST['categoria'] ?? 'Concierto';

    $imagen = $evento["imagen"];
    if (!empty($_FILES["imagen"]["name"])) {
        $imagen_nombre = time() . "_" . basename($_FILES["imagen"]["name"]);
        $ruta_imagen   = "uploads_eventos/" . $imagen_nombre;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
            $imagen = $imagen_nombre;
        }
    }
//sentencia sql para actualizar los datos del evento
    $sql_update = "
        UPDATE eventos SET
            nombre      = :nombre,
            descripcion = :descripcion,
            fecha       = :fecha,
            hora        = :hora,
            precio      = :precio,
            ubicacion   = :ubicacion,
            imagen      = :imagen,
            aforo       = :aforo,
            categoria   = :categoria
        WHERE id_evento = :id_evento AND id_empresa = :id_empresa
    ";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bindParam(":nombre",      $nombre);
    $stmt_update->bindParam(":descripcion", $descripcion);
    $stmt_update->bindParam(":fecha",       $fecha);
    $stmt_update->bindParam(":hora",        $hora);
    $stmt_update->bindParam(":precio",      $precio);
    $stmt_update->bindParam(":ubicacion",   $ubicacion);
    $stmt_update->bindParam(":imagen",      $imagen);
    $stmt_update->bindParam(":aforo",       $aforo);
    $stmt_update->bindParam(":categoria",   $categoria);
    $stmt_update->bindParam(":id_evento",   $id_evento);
    $stmt_update->bindParam(":id_empresa",  $id_empresa);

    if ($stmt_update->execute()) {
        header("Location: editar_evento.php?id=$id_evento&exito=1");
        exit();
    } else {
        $_SESSION["error"] = "Error al actualizar el evento.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Evento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen flex flex-col">

<?php include 'includes/headerContacto.php'; ?>

<main class="flex-grow flex items-center justify-center py-10 px-4">
    <div class="bg-white shadow-xl rounded-lg p-10 w-full max-w-3xl" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-blue-600 text-center mb-6 flex items-center justify-center gap-2">
            <i class="ri-edit-line text-4xl text-purple-500"></i>Editar Evento
        </h2>

        <?php if (isset($_SESSION["error"])): ?>
            <p class="text-red-500 text-center mb-4">
                <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="handleSubmit(event)">
            <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                <label class="block font-semibold">Nombre del Evento:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($evento["nombre"]); ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
            </div>

            <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                <label class="block font-semibold">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="w-full border rounded-md px-4 py-2" rows="6"><?php echo htmlspecialchars($evento["descripcion"]); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Fecha:</label>
                    <input type="date" name="fecha" value="<?php echo date('Y-m-d', strtotime($evento["fecha"])); ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Hora:</label>
                    <input type="time" name="hora" value="<?php echo $evento["hora"]; ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Precio (€):</label>
                    <input type="number" name="precio" min="0.01" step="0.01" value="<?php echo $evento["precio"]; ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Ubicación:</label>
                    <input type="text" name="ubicacion" value="<?php echo htmlspecialchars($evento["ubicacion"]); ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Aforo:</label>
                    <input type="number" name="aforo" min="1" value="<?php echo $evento["aforo"]; ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Categoría:</label>
                    <select name="categoria" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                        <?php
                        $opts = ['Concierto','Festival','Teatro','Cine','Actuaciones'];
                        foreach ($opts as $opt) {
                            $sel = ($evento['categoria'] === $opt) ? ' selected' : '';
                            echo "<option value=\"{$opt}\"{$sel}>{$opt}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Imagen Actual:</label>
                    <?php if (!empty($evento["imagen"])): ?>
                        <img src="uploads_eventos/<?php echo htmlspecialchars($evento["imagen"]); ?>" alt="Imagen actual" class="h-20 rounded-md mb-2">
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-gray-600" onchange="previewImage(event)">
                    <img id="preview" class="mt-2 hidden rounded-md max-h-40" />
                </div>
            </div>

            <button id="submitBtn" type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="ri-save-line text-xl"></i>Guardar Cambios
            </button>
        </form>
    </div>
</main>

<!-- modal para cuando se crea correctamente el evento -->
<div id="modalExito" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm text-center">
    <h3 class="text-xl font-bold text-green-600 mb-2">¡Cambios guardados!</h3>
    <p class="text-gray-700 mb-4">El evento se ha actualizado correctamente.</p>
    <button onclick="cerrarModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Aceptar</button>
  </div>
</div>

<?php include 'includes/footerContacto.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ once: true });

  function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }
  }

  function handleSubmit(e) {
    const btn = document.getElementById("submitBtn");
    btn.disabled = true;
    btn.innerHTML = `
      <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
      </svg>
      Guardando...
    `;
  }

  if (window.location.search.includes('exito=1')) {
    const modal = document.getElementById('modalExito');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function cerrarModal() {
    const modal = document.getElementById('modalExito');
    modal.classList.add('hidden');
    window.location.href = 'panel_empresa.php';
  }

  ClassicEditor
    .create(document.querySelector('#descripcion'))
    .catch(error => {
      console.error(error);
    });
</script>
</body>
</html>
