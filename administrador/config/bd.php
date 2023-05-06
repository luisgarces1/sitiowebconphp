<?php
$host="localhost";
$bd="sitio";
$usuario="Luis";
$contrasenia="Luis";

try {
$conexion=new PDO("mysql:host=$host;dbname=$bd",$usuario,$contrasenia);

} catch ( Exception $ex) {
echo $ex->getMessage();
} 
?>