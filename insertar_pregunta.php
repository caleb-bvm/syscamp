<?php
include_once('configuracion/conexion.php');
include_once('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pregunta_texto = $_POST['pregunta'];
    $categoria_seleccionada = $_POST['categoria'];

    if (!empty($pregunta_texto) && !empty($categoria_seleccionada)) {
        $query_insert = "INSERT INTO preguntas (pregunta, categoria) VALUES ($1, $2)";
        $result = pg_query_params($conexion, $query_insert, [$pregunta_texto, $categoria_seleccionada]);

        if ($result) {
            $mensaje = "Pregunta insertada correctamente.";
        } else {
            $mensaje = "Error al insertar la pregunta: " . pg_last_error($conexion);
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
    }
}
?>

<div class="container mt-5">
    <h2>Insertar Nueva Pregunta</h2><br>
    <?php if (isset($mensaje)): ?>
        <div class="alert <?php echo (strpos($mensaje, 'Error') === false) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
        <form method="POST">
            <div class="mb-3">
                <label for="pregunta" class="form-label">Pregunta:</label>
                <textarea class="form-control" id="pregunta" name="pregunta" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría:</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">Seleccionar categoría</option>
                    <option value="2.1 Ambiente de aula">2.1 Ambiente de aula</option>
                    <option value="2.2 Organización del aula">2.2 Organización del aula</option>
                    <option value="2.3 Mediación pedagógica">2.3 Mediación pedagógica</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar Pregunta</button>
        </form>
    </div>
    <br>
    <a href="listar_preguntas.php" class="btn btn-secondary"><i class="bi bi-box-arrow-left"></i> Volver a la lista de preguntas</a>
</div>

<?php include_once('footer.php'); ?>