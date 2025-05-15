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

            <div class="mb-3">
    <label for="codigo_institucion" class="form-label">Código de la institución</label>
    <input type="text" class="form-control" id="codigo_institucion" name="codigo_institucion" required>
</div>

<div class="mb-3">
    <label for="grado_seccion_turno" class="form-label">Grado y Sección</label>
    <input type="text" class="form-control" id="grado_seccion_turno" name="grado_seccion_turno" required>
</div>

<div class="mb-3">
    <label for="turno" class="form-label">Turno</label>
    <input type="text" class="form-control" id="turno" name="turno" required>
</div>

<div class="mb-3">
    <label for="numero_estudiantes" class="form-label">Número de estudiantes</label>
    <input type="number" class="form-control" id="numero_estudiantes" name="numero_estudiantes" required min="1">
</div>

<div class="mb-3">
    <label for="fecha_acompanamiento" class="form-label">Fecha de acompañamiento</label>
    <input type="date" class="form-control" id="fecha_acompanamiento" name="fecha_acompanamiento" required>
</div>


        
<script>
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');
    const distritoSelect = document.getElementById('distrito');
    const centroEducativoSelect = document.getElementById('centro_educativo'); // Obtener el nuevo select

    // Cargar municipios al cambiar el departamento
    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;

        municipioSelect.disabled = true;
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        centroEducativoSelect.disabled = true; // Deshabilitar también el select de centros educativos
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

    // Cargar distritos al cambiar el municipio
    municipioSelect.addEventListener('change', function() {
const municipioId = this.value;

distritoSelect.disabled = true;
distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
centroEducativoSelect.disabled = true; // Deshabilitar también el select de centros educativos
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

    // Cargar centros educativos al cambiar el distrito
    distritoSelect.addEventListener('change', function() {
const distritoId = this.value;

centroEducativoSelect.disabled = true; // Deshabilitar al inicio
centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';

if (distritoId) {
    fetch('obtener_centros_educativos.php?distrito_id=' + distritoId)
    .then(response => response.json())
    .then(data => {
        centroEducativoSelect.disabled = false; // Habilitar el select
        data.forEach(centro => {
        const option = document.createElement('option');
        option.value = centro.id_institucion; // Usar el ID de la institución
        option.textContent = centro.nombre_institucion; // Mostrar el nombre
        centroEducativoSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error al obtener los centros educativos:', error);
    });
}
    });
</script>


                
        
    <button type="submit" class="btn btn-primary w-100">Guardar Persona</button>
    </form>


<?php
include_once('footer.php');?>