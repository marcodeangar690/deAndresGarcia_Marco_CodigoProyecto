<?php 
require_once "conexion.php";
session_start();
//verificar que la empresa está logueada y, si no, redirigir a login
if (!isset($_SESSION["id_empresa"])) {
    header("Location: login_empresa.php");
    exit();
}

$id_empresa = $_SESSION["id_empresa"];
$sql = "SELECT nombre_empresa, email, telefono, direccion, logo FROM empresas WHERE id_empresa = :id_empresa";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(":id_empresa", $id_empresa);
$stmt->execute();
$empresa = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

//asegurarse de que la carpeta de uploads existe
$uploads_dir = "uploads/";
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

//para actualizar los datos de algún campo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["actualizar"])) {
        $nombre    = isset($_POST["nombre_empresa"]) ? trim($_POST["nombre_empresa"]) : $empresa["nombre_empresa"];
        $telefono  = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : $empresa["telefono"];
        $direccion = !empty($_POST["direccion"]) ? trim($_POST["direccion"]) : $empresa["direccion"];
        $logo_nombre = $empresa["logo"];

        if (!empty($_FILES["logo"]["name"])) {
            $file_tmp  = $_FILES["logo"]["tmp_name"];
            $file_name = time() . "_" . basename($_FILES["logo"]["name"]);
            $logo_ruta = $uploads_dir . $file_name;

            if (move_uploaded_file($file_tmp, $logo_ruta)) {
                $logo_nombre = $file_name;
            } else {
                $_SESSION["error"] = "Error al subir el logo.";
                header("Location: perfil_empresa.php");
                exit();
            }
        }
        //sentencia SQL para actualizar los campos modificados
        $sql_update = "UPDATE empresas SET nombre_empresa = :nombre, telefono = :telefono, direccion = :direccion, logo = :logo WHERE id_empresa = :id_empresa";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bindParam(":nombre",    $nombre);
        $stmt_update->bindParam(":telefono",  $telefono);
        $stmt_update->bindParam(":direccion", $direccion);
        $stmt_update->bindParam(":logo",      $logo_nombre);
        $stmt_update->bindParam(":id_empresa",$id_empresa);

        if ($stmt_update->execute()) {
            $_SESSION["nombre_empresa"] = $nombre;
            $_SESSION["mensaje"]        = "Perfil actualizado correctamente.";
            header("Location: perfil_empresa.php");
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
    <title>Perfil de Empresa</title>
    <link
      rel="icon"
      href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include 'includes/headerPerfil.php'; ?>

    <div class="flex-grow flex justify-center items-center px-4 mt-10 mb-10">
      <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-3xl">
        <h2 class="text-3xl font-bold text-blue-600 text-center mb-6">
          Perfil de Empresa
        </h2>

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
          <!-- parte del logo -->
          <div class="bg-gray-50 p-6 sm:p-8 rounded-lg shadow flex flex-col items-center">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Logo</h3>
            <img src="uploads/<?php echo htmlspecialchars($empresa['logo']); ?>" alt="Logo de empresa" class="w-32 h-32 rounded-full shadow-md bg-white object-contain">
            <form method="POST" action="" enctype="multipart/form-data" class="mt-4 w-full">
              <input type="file" name="logo" class="block w-full text-sm text-gray-500 mb-2">
              <button type="submit" name="actualizar" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Actualizar Logo</button>
            </form>
          </div>
          <div class="bg-gray-50 p-6 sm:p-8 rounded-lg shadow w-full">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Información de Empresa</h3>
            <form method="POST" action="" enctype="multipart/form-data">
              <label class="block font-medium">Nombre:</label>
              <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($empresa['nombre_empresa']); ?>" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <label class="block font-medium mt-4">Email:</label>
              <input type="email" value="<?php echo htmlspecialchars($empresa['email']); ?>" disabled class="w-full mt-1 px-4 py-2 border rounded-md bg-gray-200 cursor-not-allowed">

              <label class="block font-medium mt-4">Teléfono:</label>
              <input type="text" name="telefono" value="<?php echo htmlspecialchars($empresa['telefono']); ?>" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <label class="block font-medium mt-4">Dirección:</label>
              <input type="text" name="direccion" value="<?php echo htmlspecialchars($empresa['direccion']); ?>" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

              <button type="submit" name="actualizar" class="w-full bg-blue-600 text-white py-2 mt-6 rounded-md hover:bg-blue-700 transition">Actualizar Datos</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php include 'includes/footerContacto.php'; ?>
  </body>
</html>
