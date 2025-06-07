<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//condicional para el tipo de usuario
if (isset($_SESSION["id_usuario"])) {
    $nombreUsuario = isset($_SESSION["nombre"]) ? htmlspecialchars($_SESSION["nombre"]) : "Usuario";
    $perfilLink = "perfil.php";
} elseif (isset($_SESSION["id_empresa"])) {
    $nombreUsuario = isset($_SESSION["nombre_empresa"]) ? htmlspecialchars($_SESSION["nombre_empresa"]) : "Empresa";
    $perfilLink = "perfil_empresa.php";
} else {
    $nombreUsuario = "Invitado";
    $perfilLink = "#";
}
?>

<footer class="bg-gray-900 text-white py-6">
    <div class="container max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-start text-center md:text-left px-6 space-y-6 md:space-y-0 md:space-x-16">

        <!-- 1º columna -->
        <div class="w-full md:w-1/3 flex flex-col items-center md:items-start">
            <h2 class="text-lg font-bold text-blue-400">Evenly</h2>
            <p class="text-gray-400 text-sm">
                La mejor plataforma para gestionar tus eventos de manera eficiente y darlos a conocer al resto de personas.
            </p>
        </div>

        <!-- 2º columna -->
        <div class="w-full md:w-1/3 flex flex-col items-center">
            <h3 class="text-md font-semibold text-white mb-2">Enlaces rápidos</h3>
            <ul class="space-y-1">
                <center>
                <?php if (!isset($_SESSION["id_usuario"]) && !isset($_SESSION["id_empresa"])): ?>
                    <li><a href="login.php" class="text-gray-400 hover:text-blue-400">Iniciar Sesión Usuario</a></li>
                    <li><a href="login_empresa.php" class="text-gray-400 hover:text-blue-400">Iniciar Sesión Empresa</a></li>
                    <li><a href="registro.php" class="text-gray-400 hover:text-blue-400">Registro Usuario</a></li>
                    <li><a href="registro_empresa.php" class="text-gray-400 hover:text-blue-400">Registro Empresa</a></li>
                <?php else: ?>
                    <li><a href="eventos.php" class="text-gray-400 hover:text-blue-400">Eventos</a></li>
                    <li><a href="index.php" class="text-gray-400 hover:text-blue-400">Inicio</a></li>
                <?php endif; ?>
                </center>
            </ul>
        </div>

        <!-- 3º columna -->
        <div class="w-full md:w-1/3 flex flex-col items-center md:items-end">
            <h3 class="text-md font-semibold text-white mb-2">Contáctanos</h3>
            <ul class="space-y-1">
                <li><a href="contacto.php" class="text-gray-400 hover:text-blue-400">Contacto</a></li>
                <li><a href="legal.php" class="text-gray-400 hover:text-blue-400">Aviso Legal</a></li>
            </ul>
        </div>
    </div>

    <!-- linea separacion -->
    <div class="border-t border-gray-700 mt-4 pt-3 text-center text-gray-500 text-sm">
        &copy; <?php echo date('Y'); ?> Evenly - Todos los derechos reservados.
    </div>
</footer>
