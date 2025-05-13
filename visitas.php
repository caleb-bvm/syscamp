<?php
include_once('header.php');?>

<?php
include("configuracion/conexion.php");
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Formulario de gestor</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="departamento" class="form-label">Departamento</label>
                <select class="form-select" id="departamento" name="departamento" required>
                    <?php 
                    $query = "SELECT * FROM departamento";
                    $resultado = pg_query($conexion, $query);
                    while ($fila = pg_fetch_assoc($resultado)) {
                        echo "<option value='" . $fila['id_departamento'] . "'>" . $fila['nombre_departamento'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="distrito" class="form-label">Distrito</label>
                <select class="form-select" id="distrito" name="distrito" disabled required>
                    <option value="">Seleccione un distrito</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>

            
<script>
    const departamentoSelect = document.getElementById('departamento');
    const distritoSelect = document.getElementById('distrito');

    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;

        if (!departamentoId) {
            distritoSelect.disabled = true;
            distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
            return;
        }

        fetch('obtener_distritos.php?departamento_id=' + departamentoId)
            .then(response => response.json())
            .then(data => {
                distritoSelect.disabled = false;
                distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
                data.forEach(distrito => {
                    const option = document.createElement('option');
                    option.value = distrito.id_distrito; // Asegúrate de usar el nombre correcto de la columna del ID del distrito
                    option.textContent = distrito.nombre_distrito; // Asegúrate de usar el nombre correcto de la columna del nombre del distrito
                    distritoSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los distritos:', error);
            });
    });
</script>



<?php
include_once('footer.php');?>