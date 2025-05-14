<?php
    header('Content-Type: application/json');
    include("configuracion/conexion.php"); // Incluye tu archivo de conexión

    if (isset($_GET['municipio_id']) && is_numeric($_GET['municipio_id'])) {
        $municipio_id = $_GET['municipio_id'];

        $query = "SELECT id_distrito, nombre_distrito FROM distrito WHERE id_municipio = $municipio_id ORDER BY nombre_distrito";
        $resultado = pg_query($conexion, $query);

        $distritos = array();
        while ($fila = pg_fetch_assoc($resultado)) {
            $distritos[] = $fila;
        }

        echo json_encode($distritos);
    } else {
        echo json_encode([]);
    }
?>