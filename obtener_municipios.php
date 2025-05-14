<?php
    header('Content-Type: application/json');
    include("configuracion/conexion.php"); // Incluye tu archivo de conexión

    if (isset($_GET['departamento_id']) && is_numeric($_GET['departamento_id'])) {
        $departamento_id = $_GET['departamento_id'];

        $query = "SELECT id_municipio, nombre_municipio FROM municipio WHERE id_departamento = $departamento_id ORDER BY nombre_municipio";
        $resultado = pg_query($conexion, $query);

        $municipios = array();
        while ($fila = pg_fetch_assoc($resultado)) {
            $municipios[] = $fila;
        }

        echo json_encode($municipios);
    } else {
        echo json_encode([]);
    }
?>