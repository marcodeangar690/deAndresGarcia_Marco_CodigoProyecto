<?php
//parametros para la conexion
$host = "localhost";
$usuario = "u809842389_ocramMutual690";
$clave = "AbrimosUnWord.69";
$bd = "u809842389_tfg";

try {
    //creacion de la conexion con PDO para ser segura
    $conexion = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $usuario, $clave);
    
    //manejo de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    //mensaje para el error
    die("Error de conexión: " . $e->getMessage());
}
?>
