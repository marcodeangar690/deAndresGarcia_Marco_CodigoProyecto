<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//verificar el usuario de la sesion
$nombreUsuario = isset($_SESSION["nombre"]) && !empty($_SESSION["nombre"]) ? htmlspecialchars($_SESSION["nombre"]) : "Usuario";
$nombreEmpresa = isset($_SESSION["nombre_empresa"]) && !empty($_SESSION["nombre_empresa"]) ? htmlspecialchars($_SESSION["nombre_empresa"]) : "Empresa";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #menuCuenta, #menuEmpresa {
            z-index: 50;
        }
        .hero-section {
            position: relative;
            z-index: 0;
        }
        nav {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
<nav class="bg-gray-900 text-white p-4 relative">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-xl font-bold text-blue-400">
            <img src="images/logo.png" alt="" width="150px">
        </a>
        
        <div class="relative flex items-center">
            <a href="index.php" class="mx-3 hover:text-blue-400">Inicio</a>
            <a href="eventos.php" class="mx-3 hover:text-blue-400">Eventos</a>

            <?php if (!isset($_SESSION["id_empresa"]) && !isset($_SESSION["id_usuario"])): ?>
                <!-- desplegable parte de empresa -->
                <div class="relative inline-block">
                    <button onclick="toggleEmpresa()" class="mx-3 bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none">
                        ¿Eres una empresa?
                    </button>
                    <div id="menuEmpresa" class="absolute right-0 mt-2 w-48 bg-white text-black rounded-md shadow-lg hidden z-50">
                        <a href="registro_empresa.php" class="block px-4 py-2 hover:bg-gray-200">Registrarse</a>
                        <a href="login_empresa.php" class="block px-4 py-2 hover:bg-gray-200">Iniciar Sesión</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION["id_usuario"])): ?>

                <?php if ($_SESSION["email"] === 'contacto.evenly@gmail.com'): ?>
                    <a href="administracion.php" class="mx-3 hover:text-blue-400">
                        Administración
                    </a>
                <?php endif; ?>

                <!-- carrito solo para usuarios -->
                <a href="carrito.php"
                   class="relative mx-3 hover:text-blue-400 flex items-center">
                  <i class="ri-shopping-cart-line text-2xl"></i>
                  <span id="cartCount"
                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                    <?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>
                  </span>
                </a>

                <!-- parte usuarios -->
                <div class="relative inline-block">
                    <button id="btnCuenta" class="mx-3 bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none">
                        Mi Cuenta (<?php echo $nombreUsuario; ?>)
                    </button>
                    <div id="menuCuenta" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                        <a href="perfil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Perfil</a>
                        <a href="panel.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Panel</a>
                        <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">Cerrar Sesión</a>
                    </div>
                </div>

            <?php elseif (isset($_SESSION["id_empresa"])): ?>
                <!-- parte empresas -->
                <div class="relative inline-block">
                    <button id="btnCuenta" class="mx-3 bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none">
                        Mi Empresa (<?php echo $nombreEmpresa; ?>)
                    </button>
                    <div id="menuCuenta" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                        <a href="panel_empresa.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Panel</a>
                        <a href="perfil_empresa.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">Perfil</a>
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
        if (btnCuenta && menuCuenta) {
            btnCuenta.addEventListener("click", function () {
                menuCuenta.classList.toggle("hidden");
            });
            menuCuenta.addEventListener("mouseenter", function () {
                menuCuenta.classList.remove("hidden");
            });
            menuCuenta.addEventListener("mouseleave", function () {
                menuCuenta.classList.add("hidden");
            });
        }
    });

    function toggleEmpresa() {
        const menu = document.getElementById("menuEmpresa");
        if (menu) menu.classList.toggle("hidden");
    }
</script>
