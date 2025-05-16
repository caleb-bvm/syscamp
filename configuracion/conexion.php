<?php
// configuracion/conexion.php

$host = "localhost";
$port = "5432";
$dbname = "bootcampst13";
$user = "postgres";
$password = "ivania0807"; // <-- cambia esto por la contraseña de tu usuario

// Conexión nativa a PostgreSQL
$conexion = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conexion) {
    die("Error de conexión a la base de datos.");
}
?>
