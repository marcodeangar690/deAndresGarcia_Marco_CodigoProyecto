<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Eventos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<nav class="bg-gray-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-xl font-bold text-blue-400"><img src="images/logo.png" alt="" width="150px"></a>
        
        <div class="relative">
            <a href="index.php" class="mx-3 hover:text-blue-400">Inicio</a>

            <?php if (isset($_SESSION["id_usuario"])): ?>
                <div class="relative inline-block">
                    <button id="btnCuenta" class="mx-3 bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none">
                        Mi Cuenta (<?php echo isset($_SESSION["nombre"]) ? htmlspecialchars($_SESSION["nombre"]) : 'Usuario'; ?>)
                    </button>
                    <div id="menuCuenta" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                        <a href="perfil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Perfil</a>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="mx-3 hover:text-blue-400">Iniciar Sesión</a>
                <a href="registro.php" class="mx-3 hover:text-blue-400">Registro</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnCuenta = document.getElementById("btnCuenta");
        const menuCuenta = document.getElementById("menuCuenta");

        btnCuenta.addEventListener("click", function () {
            menuCuenta.classList.toggle("hidden");
        });

        menuCuenta.addEventListener("mouseenter", function () {
            menuCuenta.classList.remove("hidden");
        });

        menuCuenta.addEventListener("mouseleave", function () {
            menuCuenta.classList.add("hidden");
        });
    });
</script>
