<?php
include_once('configuracion/conexion.php');

// Capturar filtros desde el formulario o la URL
$categoria = $_GET['categoria'] ?? '';
$grado     = $_GET['grado'] ?? '';
$turno     = $_GET['turno'] ?? '';
$fecha     = $_GET['fecha'] ?? '';

// Construir condiciones
$condiciones = [];
if (!empty($categoria)) {
    $condiciones[] = "p.categoria ILIKE '%$categoria%'";
}
if (!empty($grado)) {
    $condiciones[] = "r.grado ILIKE '%$grado%'";
}
if (!empty($turno)) {
    $condiciones[] = "r.turno ILIKE '%$turno%'";
}
if (!empty($fecha)) {
    $condiciones[] = "r.fecha = '$fecha'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, 
           p.categoria, p.pregunta, rd.respuesta, rd.comentario
    FROM respuestas r
    JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    JOIN institucion i ON r.id_institucion = i.id_institucion
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>📋 Reporte de Visitas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h2 class="mb-4">📋 Reporte de Visitas</h2>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Categoría</label>
            <input type="text" name="categoria" value="<?= htmlspecialchars($categoria) ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Grado</label>
            <input type="text" name="grado" value="<?= htmlspecialchars($grado) ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Turno</label>
            <input type="text" name="turno" value="<?= htmlspecialchars($turno) ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger" target="_blank">Exportar a PDF</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Institución</th>
                    <th>Grado</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = pg_fetch_assoc($resultado)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre_institucion']) ?></td>
                        <td><?= htmlspecialchars($fila['grado']) ?></td>
                        <td><?= htmlspecialchars($fila['turno']) ?></td>
                        <td><?= htmlspecialchars($fila['fecha']) ?></td>
                        <td><?= htmlspecialchars($fila['categoria']) ?></td>
                        <td><?= htmlspecialchars($fila['pregunta']) ?></td>
                        <td><?= htmlspecialchars($fila['respuesta']) ?></td>
                        <td><?= htmlspecialchars($fila['comentario']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
