<?php 
require_once "conexion.php";
session_start();
//verificar que el usuario esta logeado y, si no redirigir a login
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION["id_usuario"];
$sql = "SELECT nombre, email, telefono, foto_perfil, fecha_nacimiento, direccion FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id_usuario", $id_usuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

//verificar que la carpeta existe
$uploads_dir = "uploads/";
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

//proceso para la actualizacion de los datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["actualizar"])) {
        $nombre           = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : $usuario["nombre"];
        $telefono         = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : $usuario["telefono"];
        $fecha_nacimiento = !empty($_POST["fecha_nacimiento"]) ? trim($_POST["fecha_nacimiento"]) : $usuario["fecha_nacimiento"];
        $direccion        = !empty($_POST["direccion"]) ? trim($_POST["direccion"]) : $usuario["direccion"];
        $foto_nombre      = $usuario["foto_perfil"];

        //subida de la foto de perfil
        if (!empty($_FILES["foto_perfil"]["name"])) {
            $file_tmp  = $_FILES["foto_perfil"]["tmp_name"];
            $file_name = time() . "_" . basename($_FILES["foto_perfil"]["name"]);
            $foto_ruta = $uploads_dir . $file_name;
            //para actualizar la nueva foto que se ha subido
            if (move_uploaded_file($file_tmp, $foto_ruta)) {
                $foto_nombre = $file_name; 
            } else {
                $_SESSION["error"] = "Error al subir la imagen.";
                header("Location: perfil.php");
                exit();
            }
        }

        //sentencia para actualizar los datos que se han modificado
        $sql_update = "UPDATE usuarios SET nombre = :nombre, telefono = :telefono, fecha_nacimiento = :fecha_nacimiento, direccion = :direccion, foto_perfil = :foto_perfil WHERE id_usuario = :id_usuario";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bindParam(":nombre",           $nombre);
        $stmt_update->bindParam(":telefono",         $telefono);
        $stmt_update->bindParam(":fecha_nacimiento", $fecha_nacimiento);
        $stmt_update->bindParam(":direccion",        $direccion);
        $stmt_update->bindParam(":foto_perfil",      $foto_nombre);
        $stmt_update->bindParam(":id_usuario",       $id_usuario);

        if ($stmt_update->execute()) {
            $_SESSION["nombre"]  = $nombre;
            $_SESSION["mensaje"] = "Perfil actualizado correctamente.";
            header("Location: perfil.php");
            exit();
        } else {
            $_SESSION["error"] = "Error al actualizar el perfil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil de Usuario</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include 'includes/headerPerfil.php'; ?>

    <div class="flex-grow flex justify-center items-center px-4 mt-10 mb-10">
      <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-3xl">
        <h2 class="text-3xl font-bold text-blue-600 text-center mb-6">Mi Perfil</h2>

        <?php if (isset($_SESSION["mensaje"])): ?>
          <p class="text-green-500 text-center mb-4">
            <?php echo $_SESSION["mensaje"]; unset($_SESSION["mensaje"]); ?>
          </p>
        <?php endif; ?>
        <?php if (isset($_SESSION["error"])): ?>
          <p class="text-red-500 text-center mb-4">
            <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
          </p>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- parte de foto del usuario -->
          <div class="bg-gray-50 p-6 sm:p-8 rounded-lg shadow flex flex-col items-center">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Foto de Perfil</h3>
            <img src="uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" class="w-32 h-32 rounded-full shadow-md object-cover">
            <form method="POST" action="" enctype="multipart/form-data" class="mt-4 w-full">
              <input type="file" name="foto_perfil" class="block w-full text-sm text-gray-500 mb-2">
              <button type="submit" name="actualizar" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Actualizar Foto</button>
            </form>
          </div>

          <div class="bg-gray-50 p-6 sm:p-8 rounded-lg shadow w-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información Personal</h3>
            <form method="POST" action="" enctype="multipart/form-data">
              <label class="block font-medium">Nombre:</label>
              <input type="text" name="nombre" value="<?php echo isset($_SESSION["nombre"]) ? htmlspecialchars($_SESSION["nombre"]) : htmlspecialchars($usuario['nombre']); ?>" required class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-blue-300">

              <label class="block font-medium mt-4">Email:</label>
              <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled class="w-full mt-1 px-4 py-2 border rounded-md bg-gray-200 cursor-not-allowed">

              <label class="block font-medium mt-4">Teléfono:</label>
              <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <label class="block font-medium mt-4">Fecha de Nacimiento:</label>
              <input type="date" name="fecha_nacimiento" value="<?php echo !empty($usuario['fecha_nacimiento']) ? htmlspecialchars($usuario['fecha_nacimiento']) : ''; ?>" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <label class="block font-medium mt-4">Dirección:</label>
              <input type="text" name="direccion" value="<?php echo !empty($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : ''; ?>" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <button type="submit" name="actualizar" class="w-full bg-blue-600 text-white py-2 mt-6 rounded-md hover:bg-blue-700 transition"> Actualizar Datos</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php include 'includes/footerContacto.php'; ?>
  </body>
</html>
