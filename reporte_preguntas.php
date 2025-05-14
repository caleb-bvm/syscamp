<?php
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros si existen
$filtro_codigo = $_GET['filtro_codigo'] ?? '';
$filtro_categoria = $_GET['filtro_categoria'] ?? '';

// Construir condiciones para el WHERE
$condiciones = [];
if (!empty($filtro_codigo)) {
    $condiciones[] = "cod_pregunta = '$filtro_codigo'";
}
if (!empty($filtro_categoria)) {
    $condiciones[] = "categoria = '$filtro_categoria'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL con filtros
$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas $where ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Reporte de Preguntas</h2>

    <!-- Filtros -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Filtrar por Código</label>
            <input type="text" name="filtro_codigo" class="form-control" value="<?= htmlspecialchars($filtro_codigo) ?>" placeholder="Ej: P001">
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrar por Categoría</label>
            <select name="filtro_categoria" class="form-select">
                <option value="">Todas las categorías</option>
                <?php
                // Obtener todas las categorías distintas
                $categorias_query = pg_query($conexion, "SELECT DISTINCT categoria FROM preguntas ORDER BY categoria");
                while ($cat = pg_fetch_assoc($categorias_query)) {
                    $selected = ($filtro_categoria == $cat['categoria']) ? "selected" : "";
                    echo "<option value='{$cat['categoria']}' $selected>{$cat['categoria']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4 text-end align-self-end">
            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            <a href="reporte_preguntas.php" class="btn btn-secondary">Limpiar</a>
            <a href="generar_pdf_preguntas.php?filtro_codigo=<?= $filtro_codigo ?>&filtro_categoria=<?= $filtro_categoria ?>" class="btn btn-danger" target="_blank">📄 Exportar PDF</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Código</th>
                    <th>Pregunta</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <?php if (pg_num_rows($resultado) > 0): ?>
                    <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['cod_pregunta']) ?></td>
                            <td><?= htmlspecialchars($fila['pregunta']) ?></td>
                            <td><?= htmlspecialchars($fila['categoria']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('footer.php'); ?>
