<?php
include_once('configuracion/conexion.php');
include_once('header.php');

// Consulta para obtener todas las preguntas
$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas ORDER BY cod_pregunta DESC";
$resultado = pg_query($conexion, $query);

if (!$resultado) {
    echo "Error al ejecutar la consulta: " . pg_last_error($conexion);
    exit;
}
?>

<div class="container mt-5">
    <h2>Listado de Preguntas</h2>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'eliminada'): ?>
    <div class="alert alert-success">Pregunta eliminada correctamente.</div>
<?php endif; ?>


    <a href="insertar_pregunta.php" class="btn btn-primary mb-3">➕ Insertar Nueva Pregunta</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Pregunta</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
      <tbody>
    <?php if ($resultado && pg_num_rows($resultado) > 0): ?>
        <?php while ($fila = pg_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo $fila['cod_pregunta']; ?></td>
                <td><?php echo htmlspecialchars($fila['pregunta']); ?></td>
                <td><?php echo $fila['categoria']; ?></td>
                <td>
                    <a href="editar_pregunta.php?id=<?php echo $fila['cod_pregunta']; ?>" class="btn btn-primary btn-sm" style="width: 120px;">Editar</a><br><hr>
                    <a href="eliminar_pregunta.php?id=<?php echo $fila['cod_pregunta']; ?>" class="btn btn-danger btn-sm" style="width: 120px;" onclick="return confirm('¿Estás seguro de que deseas eliminar esta pregunta?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center">No hay preguntas registradas.</td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>
</div>

<?php include_once('footer.php'); ?>
