<?php
require_once "conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre    = trim($_POST["nombre"]);
    $email     = trim($_POST["email"]);
    $password  = trim($_POST["password"]);
    $telefono  = trim($_POST["telefono"]);
    $id_rol    = 2; //el 2 es el de usuario normal

    //validación del correo para el registro
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "El email no es válido.";
    }
    //validación de contraseña para el registro
    elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        $_SESSION["error"] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.";
    }
    else {
        //encriptación de la contraseña en la base de datos
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO usuarios (nombre, email, contraseña, telefono, id_rol) 
                    VALUES (:nombre, :email, :password, :telefono, :id_rol)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":nombre",   $nombre);
            $stmt->bindParam(":email",    $email);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":id_rol",   $id_rol);
            $stmt->execute();

            $_SESSION["mensaje"] = "Registro exitoso, ahora puedes iniciar sesión.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION["error"] = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1"
    >
    <title>Registro</title>
    <link
      rel="icon"
      href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script
      src="https://cdn.tailwindcss.com"
    ></script>
  </head>
  <body class="bg-gray-100 flex flex-col min-h-screen">
    <?php include 'includes/headerLogin.php'; ?>

    <div class="flex-grow flex justify-center items-center px-4 mt-10 mb-10">
      <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl sm:text-3xl font-bold text-blue-600 text-center mb-4">Registro</h2>

        <?php if (isset($_SESSION["error"])): ?>
          <p class="text-red-500 text-center mb-4">
            <?php
              echo $_SESSION["error"];
              unset($_SESSION["error"]);
            ?>
          </p>
        <?php endif; ?>

        <form method="POST" action="">
          <label class="block font-medium">Nombre:</label>
          <input type="text" name="nombre" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

          <label class="block font-medium mt-4">Email:</label>
          <input type="email" name="email" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

          <label class="block font-medium mt-4">Contraseña:</label>
          <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

          <label class="block font-medium mt-4">Teléfono:</label>
          <input type="text" name="telefono" class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

          <button type="submit" class="w-full bg-blue-600 text-white py-2 mt-6 rounded-md hover:bg-blue-700 transition">Registrarse</button>
        </form>

        <p class="text-center mt-6 text-gray-600">¿Ya tienes cuenta?<a href="login.php" class="text-blue-600 hover:underline">Inicia sesión aquí</a></p>
      </div>
    </div>

    <?php include 'includes/footerContacto.php'; ?>
  </body>
</html>
