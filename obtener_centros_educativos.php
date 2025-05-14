<?php
    header('Content-Type: application/json');
    include("configuracion/conexion.php");

    if (isset($_GET['distrito_id']) && is_numeric($_GET['distrito_id'])) {
        $distrito_id = $_GET['distrito_id'];

        $query = "SELECT id_institucion, nombre_institucion FROM institucion WHERE id_distrito = $distrito_id ORDER BY nombre_institucion";
        $resultado = pg_query($conexion, $query);

        $centros = array();
        while ($fila = pg_fetch_assoc($resultado)) {
            $centros[] = $fila;
        }

        echo json_encode($centros);
    } else {
        echo json_encode([]);
    }
?>