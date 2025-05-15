<?php
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros desde la URL
$categoria = $_GET['categoria'] ?? '';
$grado     = $_GET['grado'] ?? '';
$turno     = $_GET['turno'] ?? '';
$fecha     = $_GET['fecha'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($categoria)) $condiciones[] = "p.categoria ILIKE '%$categoria%'";
if (!empty($grado))     $condiciones[] = "r.grado ILIKE '%$grado%'";
if (!empty($turno))     $condiciones[] = "r.turno ILIKE '%$turno%'";
if (!empty($fecha))     $condiciones[] = "r.fecha = '$fecha'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL con filtros
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


    <div class="container py-5">
        <h2 class="text-center mb-4">📋 Reporte de Visitas</h2>

        <!-- Filtros -->
        <div class="card card-filtros p-4 mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="categoria" value="<?= htmlspecialchars($categoria) ?>" class="form-control" placeholder="Categoría">
                </div>
                <div class="col-md-2">
                    <input type="text" name="grado" value="<?= htmlspecialchars($grado) ?>" class="form-control" placeholder="Grado">
                </div>
                <div class="col-md-2">
                    <input type="text" name="turno" value="<?= htmlspecialchars($turno) ?>" class="form-control" placeholder="Turno">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                </div>
            </form>
        </div>

        <!-- Botón Exportar -->
        <div class="d-flex justify-content-end mb-3">
            <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger btn-exportar">
                📄 Exportar a PDF
            </a>
        </div>

        <!-- Resultados -->
        <div class="table-responsive shadow-sm rounded bg-white p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>🏫 Institución</th>
                        <th>🎓 Grado</th>
                        <th>🕐 Turno</th>
                        <th>📅 Fecha</th>
                        <th>📚 Categoría</th>
                        <th>❓ Pregunta</th>
                        <th>✅ Respuesta</th>
                        <th>📝 Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (pg_num_rows($resultado) > 0): ?>
                        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No se encontraron resultados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include_once('footer.php'); ?>
