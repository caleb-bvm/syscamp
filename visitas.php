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
                <label for="departamento" class="form-label">Departamento del centro educativo</label>
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
                <label for="municipio" class="form-label">Municipio del centro educativo</label>
                <select class="form-select" id="municipio" name="municipio" disabled required>
                    <option value="">Seleccione un municipio</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="distrito" class="form-label">Distrito del centro educativo</label>
                <select class="form-select" id="distrito" name="id_distrito_reside" disabled required>
                    <option value="">Seleccione un distrito</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="centro_educativo" class="form-label">Centro Educativo</label>
                <select class="form-select" id="centro_educativo" name="id_centro_educativo" disabled required>
                    <option value="">Seleccione un centro educativo</option>
                </select>
            </div>

            <!-- Nuevas preguntas -->
            <div class="mb-3">
                <label for="funcion" class="form-label">¿Cuál es su principal función como gestor?</label>
                <select class="form-select" id="funcion" name="funcion" required>
                    <option value="">Seleccione una función</option>
                    <option value="Administración">Administración</option>
                    <option value="Docente">Docente</option>
                    <option value="Coordinación">Coordinación</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">¿Cuenta con equipos tecnológicos?</label>
                <select class="form-select" name="equipos_tecnologicos" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">¿El centro educativo tiene acceso a internet en todas sus áreas?</label>
                <select class="form-select" name="internet" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Sí">Sí</option>
                    <option value="No">No</option>
                    <option value="Solo en algunas áreas">Solo en algunas áreas</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="cantidad_estudiantes" class="form-label">¿Cuántos estudiantes hay en el centro educativo?</label>
                <input type="number" class="form-control" id="cantidad_estudiantes" name="cantidad_estudiantes" min="0" required>
            </div>

            <div class="mb-3">
                <label for="nivel_educativo" class="form-label">¿Cuál es el nivel educativo del centro?</label>
                <select class="form-select" id="nivel_educativo" name="nivel_educativo" required>
                    <option value="">Seleccione un nivel</option>
                    <option value="Primaria">Primaria</option>
                    <option value="Secundaria">Secundaria</option>
                    <option value="Técnico">Técnico</option>
                    <option value="Universitario">Universitario</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Guardar Persona</button>
        </form>
    </div>

<script>
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');
    const distritoSelect = document.getElementById('distrito');
    const centroEducativoSelect = document.getElementById('centro_educativo');

    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;
        municipioSelect.disabled = true;
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';

        if (departamentoId) {
            fetch('obtener_municipios.php?departamento_id=' + departamentoId)
            .then(response => response.json())
            .then(data => {
                municipioSelect.disabled = false;
                data.forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio.id_municipio;
                    option.textContent = municipio.nombre_municipio;
                    municipioSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los municipios:', error);
            });
        }
    });

    municipioSelect.addEventListener('change', function() {
        const municipioId = this.value;
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';

        if (municipioId) {
            fetch('obtener_distritos.php?municipio_id=' + municipioId)
            .then(response => response.json())
            .then(data => {
                distritoSelect.disabled = false;
                data.forEach(distrito => {
                    const option = document.createElement('option');
                    option.value = distrito.id_distrito;
                    option.textContent = distrito.nombre_distrito;
                    distritoSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los distritos:', error);
            });
        }
    });

    distritoSelect.addEventListener('change', function() {
        const distritoId = this.value;
        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';

        if (distritoId) {
            fetch('obtener_centros_educativos.php?distrito_id=' + distritoId)
            .then(response => response.json())
            .then(data => {
                centroEducativoSelect.disabled = false;
                data.forEach(centro => {
                    const option = document.createElement('option');
                    option.value = centro.id_institucion;
                    option.textContent = centro.nombre_institucion;
                    centroEducativoSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al obtener los centros educativos:', error);
            });
        }
    });
</script>

<?php
include_once('footer.php');?>
