<?php
include_once('header.php');
include_once('configuracion/conexion.php');

// Capturar filtros si existen
$filtro_rol = $_GET['filtro_rol'] ?? '';
$filtro_distrito = $_GET['filtro_distrito'] ?? '';
$filtro_departamento = $_GET['filtro_departamento'] ?? '';

// Construir consulta SQL dinámica
$condiciones = [];
if (!empty($filtro_rol)) {
    $condiciones[] = "p.id_rol = $filtro_rol";
}
if (!empty($filtro_distrito)) {
    $condiciones[] = "p.id_distrito_reside = $filtro_distrito";
}
if (!empty($filtro_departamento)) {
    $condiciones[] = "p.id_departamento_labora = $filtro_departamento";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta final
$query = "
SELECT p.codigo_persona, p.nombre_persona, p.apellido_persona, p.correo_persona,
r.nombre_rol, d.nombre_distrito, dept.nombre_departamento
FROM persona p
JOIN rol r ON p.id_rol = r.id_rol
JOIN distrito d ON p.id_distrito_reside = d.id_distrito
JOIN departamento dept ON p.id_departamento_labora = dept.id_departamento
$where
ORDER BY p.codigo_persona ASC
";

$resultado = pg_query($conexion, $query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Reporte General de Personas</h2>

    <!-- Filtros -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Filtrar por Rol</label>
            <select name="filtro_rol" class="form-select">
                <option value="">Todos los roles</option>
                <?php
                $roles = pg_query($conexion, "SELECT * FROM rol");
                while ($rol = pg_fetch_assoc($roles)) {
                    $selected = ($filtro_rol == $rol['id_rol']) ? "selected" : "";
                    echo "<option value='{$rol['id_rol']}' $selected>{$rol['nombre_rol']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrar por Distrito</label>
            <select name="filtro_distrito" class="form-select">
                <option value="">Todos los distritos</option>
                <?php
                $distritos = pg_query($conexion, "SELECT * FROM distrito");
                while ($dist = pg_fetch_assoc($distritos)) {
                    $selected = ($filtro_distrito == $dist['id_distrito']) ? "selected" : "";
                    echo "<option value='{$dist['id_distrito']}' $selected>{$dist['nombre_distrito']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Filtrar por Departamento</label>
            <select name="filtro_departamento" class="form-select">
                <option value="">Todos los departamentos</option>
                <?php
                $departamentos = pg_query($conexion, "SELECT * FROM departamento");
                while ($dept = pg_fetch_assoc($departamentos)) {
                    $selected = ($filtro_departamento == $dept['id_departamento']) ? "selected" : "";
                    echo "<option value='{$dept['id_departamento']}' $selected>{$dept['nombre_departamento']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            <a href="exportar_reporte_personas.php?filtro_rol=<?=$filtro_rol?>&filtro_distrito=<?=$filtro_distrito?>&filtro_departamento=<?=$filtro_departamento?>" class="btn btn-danger">📄 Exportar PDF</a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Código</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Distrito</th>
                    <th>Departamento</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['codigo_persona']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_persona']) ?></td>
                        <td><?= htmlspecialchars($fila['apellido_persona']) ?></td>
                        <td><?= htmlspecialchars($fila['correo_persona']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_rol']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_distrito']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_departamento']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('footer.php'); ?>
