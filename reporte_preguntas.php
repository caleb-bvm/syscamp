<?php
include_once('header.php');
include_once('configuracion/conexion.php');

$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Reporte de Preguntas</h2>

    <div class="mb-3 text-end">
        <a href="generar_pdf_preguntas.php" class="btn btn-danger" target="_blank">📄 Exportar PDF</a>
    </div>

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
                <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['cod_pregunta']) ?></td>
                        <td><?= htmlspecialchars($fila['pregunta']) ?></td>
                        <td><?= htmlspecialchars($fila['categoria']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('footer.php'); ?>
