<?php
require_once "conexion.php";
session_start();

//select para cargar las empresas si es admin
if (!empty($_SESSION["is_admin"])) {
    $stmtEmp   = $conexion->query("SELECT id_empresa, nombre_empresa FROM empresas ORDER BY nombre_empresa");
    $empresas  = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);
}

//verificar que es una cuenta de empresa o admin y que esta logueada, si no redirigir a login
if ( empty($_SESSION["id_empresa"]) && empty($_SESSION["is_admin"]) ) {
    header("Location: login.php");
    exit();
}

//parametros de los eventos para guardar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST["nombre"]);
    $descripcion = $_POST["descripcion"];
    $fecha       = $_POST["fecha"];
    $hora        = $_POST["hora"];
    $precio      = floatval($_POST["precio"]);
    $ubicacion   = trim($_POST["ubicacion"]);
    $aforo       = intval($_POST["aforo"]);
    if (!empty($_SESSION["is_admin"]) && !empty($_POST["id_empresa"])) {
        //si es admin selecciona la empresa a la que le va a crear el evento
        $id_empresa = intval($_POST["id_empresa"]);
    } else {
        //propia sesion para las empresas
        $id_empresa = $_SESSION["id_empresa"];
    }

    $categoria = $_POST['categoria'] ?? 'Concierto';
    //para tratar las imagenes que se suban de los eventos
    $imagen = null;
    if (!empty($_FILES["imagen"]["name"])) {
        $imagen_nombre = time() . "_" . basename($_FILES["imagen"]["name"]);
        $ruta_imagen   = "uploads_eventos/" . $imagen_nombre;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
            $imagen = $imagen_nombre;
        }
    }
    //sentencia SQL para la creacion del evento
    $sql = "INSERT INTO eventos
              (nombre, descripcion, fecha, hora, precio, ubicacion, imagen, aforo, id_empresa, categoria)
            VALUES
              (:nombre, :descripcion, :fecha, :hora, :precio, :ubicacion, :imagen, :aforo, :id_empresa, :categoria)";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":nombre",      $nombre);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":fecha",       $fecha);
    $stmt->bindParam(":hora",        $hora);
    $stmt->bindParam(":precio",      $precio);
    $stmt->bindParam(":ubicacion",   $ubicacion);
    $stmt->bindParam(":imagen",      $imagen);
    $stmt->bindParam(":aforo",       $aforo);
    $stmt->bindParam(":id_empresa",  $id_empresa);
    $stmt->bindParam(":categoria",   $categoria);

    if ($stmt->execute()) {
        $_SESSION["mensaje"] = "Evento añadido correctamente.";

        //redireccion en funcion del tipo de usuario
        if (!empty($_SESSION["is_admin"])) {
            header("Location: administracion.php?exito=1");
        } else {
            header("Location: panel_empresa.php?exito=1");
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Añadir Evento</title>
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
        <h2 class="text-3xl font-bold text-blue-600 text-center mb-6 flex items-center justify-center gap-2"><i class="ri-calendar-add-line text-4xl text-purple-500"></i>Añadir Nuevo Evento</h2>

        <?php if (isset($_SESSION["error"])): ?>
            <p class="text-red-500 text-center mb-4">
                <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="handleSubmit(event)">
            <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                <label class="block font-semibold">Nombre del Evento:</label>
                <input type="text" name="nombre" placeholder="Ej. Festival de verano 2025" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
            </div>

            <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                <label class="block font-semibold">Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="w-full border rounded-md px-4 py-2" rows="6"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Fecha:</label>
                    <input type="date" name="fecha" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Hora:</label>
                    <input type="time" name="hora" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Precio (€):</label>
                    <input type="number" name="precio" min="0.01" step="0.01" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Ubicación:</label>
                    <input type="text" name="ubicacion" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Aforo:</label>
                    <input type="number" name="aforo" min="1" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                </div>
                <?php if (!empty($_SESSION["is_admin"])): ?>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Empresa propietaria:</label>
                    <select name="id_empresa" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                    <?php foreach ($empresas as $e): ?>
                        <option value="<?= $e['id_empresa'] ?>">
                          <?= htmlspecialchars($e['nombre_empresa']) ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Categoría:</label>
                    <select name="categoria" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">
                        <option value="Concierto">Concierto</option>
                        <option value="Festival">Festival</option>
                        <option value="Teatro">Teatro</option>
                        <option value="Cine">Cine</option>
                        <option value="Actuaciones">Actuaciones</option>
                    </select>
                </div>
                <div class="bg-blue-50 p-4 rounded-md shadow-sm">
                    <label class="block font-semibold">Imagen del Evento:</label>
                    <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-gray-600" onchange="previewImage(event)">
                    <img id="preview" class="mt-2 hidden rounded-md max-h-40" />
                </div>
            </div>

            <button id="submitBtn" type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="ri-upload-line text-xl"></i>
                Añadir Evento
            </button>
        </form>
    </div>
</main>

<!-- modal para cuando se crea correctamente el evento -->
<div id="modalExito" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm text-center">
    <h3 class="text-xl font-bold text-green-600 mb-2">¡Evento añadido!</h3>
    <p class="text-gray-700 mb-4">Tu evento se ha registrado correctamente.</p>
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
      Enviando...
    `;
  }

  if (window.location.search.includes('exito=1')) {
    const modal = document.getElementById('modalExito');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  const isAdmin = <?= !empty($_SESSION["is_admin"]) ? 'true' : 'false' ?>;

  function cerrarModal() {
    const modal = document.getElementById('modalExito');
    modal.classList.add('hidden');
    if (isAdmin) {
      window.location.href = 'administracion.php';
    } else {
      window.location.href = 'panel_empresa.php';
    }
  }

  ClassicEditor
    .create(document.querySelector('#descripcion'))
    .catch(error => {
      console.error(error);
    });
</script>
</body>
</html>
