<?php
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros desde la URL
$institucion = $_GET['institucion'] ?? '';
$grado       = $_GET['grado'] ?? '';
$turno       = $_GET['turno'] ?? '';
$fecha       = $_GET['fecha'] ?? '';
$username    = $_GET['username'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($institucion)) $condiciones[] = "i.nombre_institucion ILIKE '%$institucion%'";
if (!empty($grado))       $condiciones[] = "r.grado ILIKE '%$grado%'";
if (!empty($turno))       $condiciones[] = "r.turno ILIKE '%$turno%'";
if (!empty($fecha))       $condiciones[] = "r.fecha = '$fecha'";
if (!empty($username))    $condiciones[] = "per.username ILIKE '%$username%'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, 
           p.categoria, p.pregunta, rd.respuesta, rd.comentario,
           per.username
    FROM respuestas r
    JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    JOIN institucion i ON r.id_institucion = i.id_institucion
    JOIN persona per ON id_persona =id_persona
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);
?>

<div class="container mt-4">
    <h2>📋 Reporte de Visitas</h2>

    <!-- Formulario de filtros -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="institucion" value="<?= htmlspecialchars($institucion) ?>" class="form-control" placeholder="Institución">
        </div>
        <div class="col-md-2">
            <input type="text" name="grado" value="<?= htmlspecialchars($grado) ?>" class="form-control" placeholder="Grado">
        </div>
        <div class="col-md-2">
            <input type="text" name="turno" value="<?= htmlspecialchars($turno) ?>" class="form-control" placeholder="Turno">
        </div>
        <div class="col-md-2">
            <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" placeholder="Username">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</a>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>Username</th>
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
            <?php if (pg_num_rows($resultado) > 0): ?>
                <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['username']) ?></td>
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
            <?php else: ?>
                <tr><td colspan="9" class="text-center">No se encontraron resultados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('footer.php'); ?>
