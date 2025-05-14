<?php
include_once('configuracion/conexion.php');

$cod_pregunta = $_GET['id'] ?? null;

if ($cod_pregunta) {
    $query_delete = "DELETE FROM preguntas WHERE cod_pregunta = $1";
    $result = pg_query_params($conexion, $query_delete, [$cod_pregunta]);

    if ($result) {
        header("Location: listar_preguntas.php?mensaje=eliminada");
        exit;
    } else {
        echo "Error al eliminar la pregunta: " . pg_last_error($conexion);
    }
} else {
    echo "ID de pregunta no válido.";
}
?>
