<?php
// obtener_detalles_visita.php
include_once('configuracion/conexion.php');

$cod_respuesta = $_GET['cod_respuesta'] ?? null;

if (!$cod_respuesta) {
    echo '<div class="alert alert-danger">ID de respuesta no proporcionado.</div>';
    exit();
}

// 1. Consulta para obtener los datos generales del formulario (incluyendo el director)
$query_formulario_general = "
    SELECT
        r.cod_respuesta,
        i.nombre_institucion,
        i.codigo_de_infraestructura,
        r.grado,
        r.turno,
        r.fecha,
        r.director, -- ¡AQUÍ ESTÁ EL DIRECTOR!
        per.username,
        dpt.nombre_departamento,
        mun.nombre_municipio,
        dis.nombre_distrito
    FROM
        respuestas r
    JOIN
        institucion i ON r.id_institucion = i.id_institucion
    JOIN
        persona per ON r.codigo_persona = per.id_persona
    JOIN
        distrito dis ON i.id_distrito = dis.id_distrito
    JOIN
        municipio mun ON dis.id_municipio = mun.id_municipio
    JOIN
        departamento dpt ON mun.id_departamento = dpt.id_departamento
    WHERE
        r.cod_respuesta = $cod_respuesta
";
$resultado_formulario_general = pg_query($conexion, $query_formulario_general);
$datos_formulario = pg_fetch_assoc($resultado_formulario_general);

if (!$datos_formulario) {
    echo '<div class="alert alert-warning">No se encontró el formulario con el ID proporcionado.</div>';
    exit();
}

// 2. Consulta para obtener las preguntas y respuestas detalladas
$query_detalles = "
    SELECT p.categoria, p.pregunta, rd.respuesta, rd.comentario
    FROM respuestas_detalladas rd
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    WHERE rd.respuestas_cod_respuesta = $cod_respuesta
    ORDER BY p.categoria, p.cod_pregunta
";
$resultado_detalles = pg_query($conexion, $query_detalles);

if (!$resultado_detalles) {
    echo '<div class="alert alert-danger">Error al cargar los detalles: ' . pg_last_error($conexion) . '</div>';
    exit();
}

?>

<h4 class="mb-4">📋 Vista Previa del Formulario #<?php echo htmlspecialchars($cod_respuesta); ?></h4>

<div class="card p-3 mb-4 shadow-sm">
    <h5 class="card-title">Información General del Centro Educativo</h5>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>Usuario:</strong> <?php echo htmlspecialchars($datos_formulario['username']); ?></li>
        <li class="list-group-item"><strong>Institución:</strong> <?php echo htmlspecialchars($datos_formulario['nombre_institucion']); ?></li>
        <li class="list-group-item"><strong>Código de Infraestructura:</strong> <?php echo htmlspecialchars($datos_formulario['codigo_de_infraestructura']); ?></li>
        <li class="list-group-item"><strong>Departamento:</strong> <?php echo htmlspecialchars($datos_formulario['nombre_departamento']); ?></li>
        <li class="list-group-item"><strong>Municipio:</strong> <?php echo htmlspecialchars($datos_formulario['nombre_municipio']); ?></li>
        <li class="list-group-item"><strong>Distrito:</strong> <?php echo htmlspecialchars($datos_formulario['nombre_distrito']); ?></li>
        <li class="list-group-item"><strong>Grado:</strong> <?php echo htmlspecialchars($datos_formulario['grado']); ?></li>
        <li class="list-group-item"><strong>Turno:</strong> <?php echo htmlspecialchars($datos_formulario['turno']); ?></li>
        <li class="list-group-item"><strong>Fecha de Acompañamiento:</strong> <?php echo htmlspecialchars($datos_formulario['fecha']); ?></li>
        <li class="list-group-item"><strong>Nombre del Director:</strong> <?php echo htmlspecialchars($datos_formulario['director']); ?></li>
    </ul>
</div>

<h5 class="mt-3 mb-3">Detalles de Preguntas y Respuestas:</h5>
<?php if (pg_num_rows($resultado_detalles) > 0) : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Categoría</th>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila_detalle = pg_fetch_assoc($resultado_detalles)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila_detalle['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($fila_detalle['pregunta']); ?></td>
                        <td><?php echo htmlspecialchars($fila_detalle['respuesta']); ?></td>
                        <td><?php echo htmlspecialchars($fila_detalle['comentario']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="alert alert-warning">No se encontraron detalles de preguntas y respuestas para este formulario.</div>
<?php endif; ?>

<?php
pg_close($conexion);
?>