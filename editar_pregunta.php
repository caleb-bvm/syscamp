<?php
include_once('configuracion/conexion.php');
include_once('header.php');

$cod_pregunta = $_GET['id'] ?? null;
$mensaje = '';
$mensaje_error = '';
$pregunta_data = null;

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cod_pregunta) {
    $pregunta_texto = $_POST['pregunta'];
    $categoria_seleccionada = $_POST['categoria'];

    if (!empty($pregunta_texto) && !empty($categoria_seleccionada)) {
        $query_update = "UPDATE preguntas SET pregunta = $1, categoria = $2 WHERE cod_pregunta = $3";
        $result = pg_query_params($conexion, $query_update, [$pregunta_texto, $categoria_seleccionada, $cod_pregunta]);

        if ($result) {
            $mensaje = "Pregunta actualizada correctamente.";
        } else {
            $mensaje = "Error al actualizar la pregunta: " . pg_last_error($conexion);
        }
    } else {
        $mensaje = "Por favor, completa todos los campos.";
    }
}

// Obtener los datos de la pregunta para mostrar en el formulario
if ($cod_pregunta) {
    $query_select = "SELECT cod_pregunta, pregunta, categoria FROM preguntas WHERE cod_pregunta = $1";
    $result_select = pg_query_params($conexion, $query_select, [$cod_pregunta]);

    if ($result_select && pg_num_rows($result_select) > 0) {
        $pregunta_data = pg_fetch_assoc($result_select);
    } else {
        $mensaje_error = "Pregunta no encontrada.";
    }
}
?>

<div class="container mt-5">
    <h2>Editar Pregunta</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert <?php echo (strpos($mensaje, 'Error') === false) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensaje_error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $mensaje_error; ?>
        </div>
    <?php endif; ?>

    <?php if ($pregunta_data): ?>
        <form method="POST">
            <div class="mb-3">
                <label for="pregunta" class="form-label">Pregunta:</label>
                <textarea class="form-control" id="pregunta" name="pregunta" rows="3" required><?php echo htmlspecialchars($pregunta_data['pregunta']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría:</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">Seleccionar categoría</option>
                    <option value="2.1 Ambiente de aula" <?php if ($pregunta_data['categoria'] === '2.1 Ambiente de aula') echo 'selected'; ?>>2.1 Ambiente de aula</option>
                    <option value="2.2 Organización del aula" <?php if ($pregunta_data['categoria'] === '2.2 Organización del aula') echo 'selected'; ?>>2.2 Organización del aula</option>
                    <option value="2.3 Mediación pedagógica" <?php if ($pregunta_data['categoria'] === '2.3 Mediación pedagógica') echo 'selected'; ?>>2.3 Mediación pedagógica</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
        <br>
        <a href="listar_preguntas.php" class="btn btn-secondary">Volver a la lista de preguntas</a>
    <?php endif; ?>
</div>

<?php include_once('footer.php'); ?>
