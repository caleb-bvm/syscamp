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

    <a href="insertar_pregunta.php" class="btn btn-success mb-3">Insertar Nueva Pregunta</a>

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
                            <a href="editar_pregunta.php?id=<?php echo $fila['cod_pregunta']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <!-- Puedes agregar también botón de eliminar si lo deseas -->
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
