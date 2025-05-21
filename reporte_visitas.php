<?php
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros desde la URL
$institucion = $_GET['institucion'] ?? '';
$grado       = $_GET['grado'] ?? '';
$turno       = $_GET['turno'] ?? '';
$fecha       = $_GET['fecha'] ?? '';
$username    = $_GET['username'] ?? '';

// Construir condiciones dinámicamente para la consulta principal de formularios
$condiciones = [];
if (!empty($institucion)) $condiciones[] = "i.nombre_institucion ILIKE '%" . pg_escape_string($institucion) . "%'";
if (!empty($grado))       $condiciones[] = "r.grado ILIKE '%" . pg_escape_string($grado) . "%'";
if (!empty($turno))       $condiciones[] = "r.turno ILIKE '%" . pg_escape_string($turno) . "%'";
if (!empty($fecha))       $condiciones[] = "r.fecha = '" . pg_escape_string($fecha) . "'";
if (!empty($username))    $condiciones[] = "per.username ILIKE '%" . pg_escape_string($username) . "%'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL para obtener los datos generales de cada respuesta/formulario
$query_formularios = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, per.username
    FROM respuestas r
    JOIN institucion i ON r.id_institucion = i.id_institucion
    JOIN persona per ON r.codigo_persona = per.id_persona
    $where
    ORDER BY r.fecha DESC, per.username
";
$resultado_formularios = pg_query($conexion, $query_formularios);

if (!$resultado_formularios) {
    die("Error al ejecutar la consulta principal: " . pg_last_error($conexion));
}
?>

<div class="container mt-4">
    <br><h2 class="text-center mb-4">📋 Reporte de Visitas</h2>

    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label for="institucion" class="form-label">Institución</label>
            <input type="text" id="institucion" name="institucion" value="<?= htmlspecialchars($institucion) ?>" class="form-control" placeholder="Institución">
        </div>
        <div class="col-md-2">
            <label for="grado" class="form-label">Grado</label>
            <input type="text" id="grado" name="grado" value="<?= htmlspecialchars($grado) ?>" class="form-control" placeholder="Grado">
        </div>
        <div class="col-md-2">
            <label for="turno" class="form-label">Turno</label>
            <input type="text" id="turno" name="turno" value="<?= htmlspecialchars($turno) ?>" class="form-control" placeholder="Turno">
        </div>
        <div class="col-md-2">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" placeholder="Username">
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
        </div>

        <div class="col-12 d-flex justify-content-center gap-2 mt-3">
            <a href="reporte_visitas.php" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Limpiar</a>
            <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Exportar PDF General
            </a>
        </div>
    </form>

    <div class="table-responsive p-4 shadow-sm" style="background-color: var(--bs-body-bg-rgb, white);">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Institución</th>
                    <th>Grado</th>
                    <th>Turno</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (pg_num_rows($resultado_formularios) > 0): ?>
                <?php while ($fila_formulario = pg_fetch_assoc($resultado_formularios)): ?>
                    <tr data-cod-respuesta="<?= htmlspecialchars($fila_formulario['cod_respuesta']) ?>">
                        <td><?= htmlspecialchars($fila_formulario['username']) ?></td>
                        <td><?= htmlspecialchars($fila_formulario['nombre_institucion']) ?></td>
                        <td><?= htmlspecialchars($fila_formulario['grado']) ?></td>
                        <td><?= htmlspecialchars($fila_formulario['turno']) ?></td>
                        <td><?= htmlspecialchars($fila_formulario['fecha']) ?></td>
                        <td>
                            <button class="btn btn-info btn-sm btn-ver-detalles" data-cod-respuesta="<?= htmlspecialchars($fila_formulario['cod_respuesta']) ?>">
                                <i class="bi bi-eye"></i> Ver ‎ ‎ Detalles
                            </button>
                            <a href="exportar_visita_individual_pdf.php?cod_respuesta=<?= htmlspecialchars($fila_formulario['cod_respuesta']) ?>" class="btn btn-danger btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                            </a>
                        </td>
                    </tr>
                    <tr class="detalles-fila" id="detalles-<?= htmlspecialchars($fila_formulario['cod_respuesta']) ?>" style="display: none;">
                        <td colspan="6">
                            <div class="detalles-contenido">
                                Cargando detalles...
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No se encontraron formularios con los filtros aplicados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botonesVerDetalles = document.querySelectorAll('.btn-ver-detalles');

    botonesVerDetalles.forEach(button => {
        button.addEventListener('click', function() {
            const codRespuesta = this.dataset.codRespuesta;
            const detallesFila = document.getElementById(`detalles-${codRespuesta}`);
            const detallesContenido = detallesFila.querySelector('.detalles-contenido');

            if (detallesFila.style.display === 'none') {
                // Si está oculto, mostrar y cargar/mostrar detalles
                detallesFila.style.display = 'table-row';
                // Cambiar el texto del botón
                this.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar Detalles';

                // Si los detalles no han sido cargados (es la primera vez)
                if (detallesContenido.innerHTML === 'Cargando detalles...') {
                    // ¡ASEGÚRATE DE QUE ESTA RUTA ES CORRECTA!
                    fetch(`obtener_detalles_visita.php?cod_respuesta=${codRespuesta}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok ' + response.statusText);
                            }
                            return response.text();
                        })
                        .then(data => {
                            detallesContenido.innerHTML = data;
                        })
                        .catch(error => {
                            console.error('Error al cargar los detalles:', error);
                            detallesContenido.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles. Verifique la consola para más información.</div>';
                        });
                }
            } else {
                // Si está visible, ocultar
                detallesFila.style.display = 'none';
                // Cambiar el texto del botón de nuevo
                this.innerHTML = '<i class="bi bi-eye"></i> Ver Detalles';
            }
        });
    });
});
</script>

<?php include_once('footer.php'); ?>