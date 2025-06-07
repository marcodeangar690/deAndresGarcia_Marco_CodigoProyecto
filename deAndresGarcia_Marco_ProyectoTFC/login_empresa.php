<?php
require_once "conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cif = trim($_POST["cif"]);
    $password = trim($_POST["password"]);

    //verificar intentos fallidos en el login para bloqueo
    if (!isset($_SESSION["intentos_empresa"])) {
        $_SESSION["intentos_empresa"] = 0;
        $_SESSION["bloqueo_empresa"] = time();
    }

    //si ha intentado mas de 3 veces, bloqueo durante 10 minutos
    if ($_SESSION["intentos_empresa"] >= 3 && (time() - $_SESSION["bloqueo_empresa"]) < 600) {
        $_SESSION["error"] = "Has superado el límite de intentos. Intenta de nuevo en 10 minutos.";
    } else {
        try {
            $sql = "SELECT * FROM empresas WHERE cif = :cif";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":cif", $cif);
            $stmt->execute();
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($empresa && password_verify($password, $empresa["contraseña"])) {
                $_SESSION["id_empresa"]      = $empresa["id_empresa"];
                $_SESSION["nombre_empresa"]  = $empresa["nombre_empresa"];

                //restablecer intentos
                $_SESSION["intentos_empresa"] = 0;
                $_SESSION["bloqueo_empresa"]  = time();

                header("Location: index.php");
                exit();
            } else {
                $_SESSION["intentos_empresa"]++;
                if ($_SESSION["intentos_empresa"] >= 3) {
                    $_SESSION["bloqueo_empresa"] = time();
                }
                $_SESSION["error"] = "CIF o contraseña incorrectos.";
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
    <title>Login Empresa</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='32'%20height='32'%3E%3Crect%20width='100%25'%20height='100%25'%20fill='black'/%3E%3Ctext%20x='50%25'%20y='50%25'%20font-family='Arial,Helvetica,sans-serif'%20font-size='20'%20fill='white'%20text-anchor='middle'%20dominant-baseline='central'%3EE%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <?php include 'includes/headerLoginEmpresa.php'; ?>

    <div class="flex-grow flex justify-center items-center px-4 mt-10 mb-10">
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl sm:text-3xl font-bold text-blue-600 text-center mb-4">
                Iniciar Sesión - Empresa
            </h2>

            <?php if (isset($_SESSION["error"])): ?>
                <p class="text-red-500 text-center mb-4">
                    <?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="">
                <label class="block font-medium">CIF:</label>
                <input type="text" name="cif" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

                <label class="block font-medium mt-4">Contraseña:</label>
                <input type="password" name="password" required class="w-full mt-1 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">

                <button type="submit" class="w-full bg-blue-600 text-white py-2 mt-6 rounded-md hover:bg-blue-700 transition">Iniciar Sesión</button>
            </form>
            <p class="text-center mt-6 text-gray-600">¿No estás registrado?<a href="registro_empresa.php" class="text-blue-600 hover:underline">Regístrate aquí</a></p>
        </div>
    </div>

    <?php include 'includes/footerContacto.php'; ?>

</body>
</html>
