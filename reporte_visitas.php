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
    JOIN persona per ON id_persona = id_persona
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📋 Reporte de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #004085;
        }
        .table thead {
            background-color: #004085;
            color: white;
        }
        .btn-export {
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="card p-4">
        <h2 class="text-center mb-4">📋 Reporte de Visitas</h2>

        <!-- Formulario de filtros -->
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Institución</label>
                <input type="text" name="institucion" value="<?= htmlspecialchars($institucion) ?>" class="form-control">
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
            <div class="col-md-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control">
            </div>
            <div class="col-12 d-grid">
                <button type="submit" class="btn btn-primary">🔍 Buscar</button>
            </div>
        </form>

        <!-- Botón exportar -->
        <div class="text-end mt-3">
            <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger btn-export">
                📄 Exportar PDF
            </a>
        </div>

        <!-- Tabla de resultados -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover align-middle">
                <thead>
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
                    <tr>
                        <td colspan="9" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

<?php include_once('footer.php'); ?>
