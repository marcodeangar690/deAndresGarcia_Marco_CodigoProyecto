<?php
require_once "conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    //verificar intentos fallidos en el login para bloqueo
    if (!isset($_SESSION["intentos"])) {
        $_SESSION["intentos"] = 0;
        $_SESSION["bloqueo"] = time();
    }

    //si ha intentado mas de 3 veces, bloqueo durante 10 minutos
    if ($_SESSION["intentos"] >= 3 && (time() - $_SESSION["bloqueo"]) < 600) {
        $_SESSION["error"] = "Has superado el límite de intentos. Intenta de nuevo en 10 minutos.";
    } else {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario["contraseña"])) {
                $_SESSION["id_usuario"] = $usuario["id_usuario"];
                $_SESSION["nombre"]     = $usuario["nombre"];
                $_SESSION["id_rol"]     = $usuario["id_rol"];
                $_SESSION["email"]      = $usuario["email"];

                //verificar si tiene el rol de admin
                if ($_SESSION["id_rol"] == 1) {
                    $_SESSION["is_admin"] = true;
                } else {
                    unset($_SESSION["is_admin"]);
                }

                //restablecer intentos al iniciar sesion correctamente
                $_SESSION["intentos"] = 0;
                $_SESSION["bloqueo"]  = time();

                header("Location: index.php");
                exit();
            } else {
                $_SESSION["intentos"]++;
                if ($_SESSION["intentos"] >= 3) {
                    $_SESSION["bloqueo"] = time();
                }
                $_SESSION["error"] = "Email o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $_SESSION["error"] = "Error en la autenticación: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <?php include 'includes/headerLogin.php'; ?>

    <div class="flex-grow flex justify-center items-center px-4 mt-10 mb-10">
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl sm:text-3xl font-bold text-blue-600 text-center mb-4">Iniciar Sesión</h2>

            <?php if (isset($_SESSION["error"])): ?>
                <p class="text-red-500 text-center mb-4">
                    <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="">
                <label class="block font-medium">Email:</label>
                <input type="email" name="email" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

                <label class="block font-medium mt-4">Contraseña:</label>
                <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

                <button type="submit" class="w-full bg-blue-600 text-white py-2 mt-6 rounded-md hover:bg-blue-700 transition">Iniciar Sesión</button>
            </form>

            <p class="text-center mt-6 text-gray-600">¿No tienes cuenta?<a href="registro.php" class="text-blue-600 hover:underline">Regístrate aquí</a></p>

            <!--
            <p class="text-center mt-3 text-gray-600">
                <a href="recuperar.php" class="text-blue-600 hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            </p>
            -->
        </div>
    </div>

    <?php include 'includes/footerContacto.php'; ?>

</body>
</html>
