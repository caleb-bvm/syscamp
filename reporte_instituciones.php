<?php 
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros
$filtro_departamento = $_GET['filtro_departamento'] ?? '';
$filtro_municipio = $_GET['filtro_municipio'] ?? '';
$filtro_zona = $_GET['filtro_zona'] ?? '';
$filtro_sector = $_GET['filtro_sector'] ?? '';

// Condiciones dinámicas
$condiciones = [];
if (!empty($filtro_departamento)) {
    $condiciones[] = "id_departamento = $filtro_departamento";
}
if (!empty($filtro_municipio)) {
    $condiciones[] = "id_municipio = $filtro_municipio";
}
if (!empty($filtro_zona)) {
    $condiciones[] = "zona_institucion = '$filtro_zona'";
}
if (!empty($filtro_sector)) {
    $condiciones[] = "sector_institucion = '$filtro_sector'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

$query = "SELECT * FROM instituciones $where ORDER BY codigo_de_infraestructura ASC";
$resultado = pg_query($conexion, $query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">🏫 Reporte General de Instituciones</h2>

    <!-- Filtros -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Departamento</label>
            <select name="filtro_departamento" class="form-select">
                <option value="">Todos</option>
                <?php
                $depts = pg_query($conexion, "SELECT * FROM departamento ORDER BY nombre_departamento");
                while ($dept = pg_fetch_assoc($depts)) {
                    $selected = ($filtro_departamento == $dept['id_departamento']) ? "selected" : "";
                    echo "<option value='{$dept['id_departamento']}' $selected>{$dept['nombre_departamento']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Municipio</label>
            <select name="filtro_municipio" class="form-select">
                <option value="">Todos</option>
                <?php
                $munis = pg_query($conexion, "SELECT * FROM municipio ORDER BY nombre_municipio");
                while ($mun = pg_fetch_assoc($munis)) {
                    $selected = ($filtro_municipio == $mun['id_municipio']) ? "selected" : "";
                    echo "<option value='{$mun['id_municipio']}' $selected>{$mun['nombre_municipio']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Zona</label>
            <select name="filtro_zona" class="form-select">
                <option value="">Todas</option>
                <option value="urbana" <?= $filtro_zona == 'urbana' ? 'selected' : '' ?>>Urbana</option>
                <option value="rural" <?= $filtro_zona == 'rural' ? 'selected' : '' ?>>Rural</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Sector</label>
            <select name="filtro_sector" class="form-select">
                <option value="">Todos</option>
                <option value="público" <?= $filtro_sector == 'público' ? 'selected' : '' ?>>Público</option>
                <option value="privado" <?= $filtro_sector == 'privado' ? 'selected' : '' ?>>Privado</option>
            </select>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Aplicar Filtros</button>
            <a href="generar_pdf_instituciones.php?filtro_departamento=<?= $filtro_departamento ?>&filtro_municipio=<?= $filtro_municipio ?>&filtro_zona=<?= $filtro_zona ?>&filtro_sector=<?= $filtro_sector ?>" class="btn btn-danger" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Distrito</th>
                    <th>Zona</th>
                    <th>Sector</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['codigo_de_infraestructura']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_institucion']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_departamento']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_municipio']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_distrito']) ?></td>
                        <td><?= htmlspecialchars($fila['zona_institucion']) ?></td>
                        <td><?= htmlspecialchars($fila['sector_institucion']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('footer.php'); ?>
