<?php
include_once('header.php');?>

<?php
include("configuracion/conexion.php");

// Procesar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rol = $_POST['id_rol'];
    $codigo_persona = $_POST['codigo_persona'];
    $correo_persona = $_POST['correo_persona'];
    $clave_persona = $_POST['clave_persona'];
    $nombre_persona = $_POST['nombre_persona'];
    $apellido_persona = $_POST['apellido_persona'];
    $documento_de_identificacion = $_POST['documento_de_identificacion'];
    $id_distrito_reside = $_POST['id_distrito_reside'];
    $id_departamento_labora = $_POST['id_departamento_labora'];
    $username = $_POST['username'];

    $query = "INSERT INTO persona (
        id_rol, codigo_persona, correo_persona, clave_persona,
        nombre_persona, apellido_persona, documento_de_identificacion,
        id_distrito_reside, id_departamento_labora, username
    ) VALUES (
        $id_rol, '$codigo_persona', '$correo_persona', '$clave_persona',
        '$nombre_persona', '$apellido_persona', '$documento_de_identificacion',
        $id_distrito_reside, $id_departamento_labora, '$username'
    )";

    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        $mensaje = "✅ Persona insertada correctamente.";
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
?>


<div class="container mt-5">
    <h2 class="text-center mb-4">📄 Formulario de Registro de Persona</h2><br>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-dark text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
        <h3 class="mt-4 mb-3">Datos del Gestor</h3>
        <div class="mb-3">
            <label class="form-label">Código Persona</label>
            <input type="text" class="form-control" name="codigo_persona" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" name="correo_persona" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="clave_persona" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" class="form-control" name="nombre_persona" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellido_persona" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Documento de Identificación</label>
            <input type="text" class="form-control" name="documento_de_identificacion" required>
        </div>

        <!-- Rol dinámico -->
        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select class="form-select" name="id_rol" required>
                <option value="">Selecciona un rol</option>
                <?php
                $roles = pg_query($conexion, "SELECT * FROM rol");
                while ($rol = pg_fetch_assoc($roles)) {
                    echo "<option value='{$rol['id_rol']}'>{$rol['nombre_rol']}</option>";
                }
                ?>
            </select>
        </div>

         <form method="post" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="departamento" class="form-label">Departamento de residencia</label>
                <select class="form-select" id="departamento" name="departamento" required>
                     <option value="">Seleccione un departamento</option>
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
                <label for="municipio" class="form-label">Municipio</label>
                <select class="form-select" id="municipio" name="municipio" disabled required>
                    <option value="">Seleccione un municipio</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="distrito" class="form-label">Distrito</label>
                <select class="form-select" id="distrito" name="id_distrito_reside" disabled required>
                    <option value="">Seleccione un distrito</option>
                </select>
            </div>
<script>
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');
    const distritoSelect = document.getElementById('distrito');

    // Cargar municipios al cambiar el departamento
    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;

        municipioSelect.disabled = true;
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';

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

        if (municipioId) {
            fetch('obtener_distritos.php?municipio_id=' + municipioId) // Nuevo archivo PHP
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
</script>

        <!-- Departamento dinámico -->
        <div class="mb-3">
            <label class="form-label">Departamento donde labora</label>
            <select class="form-select" name="id_departamento_labora" required>
                <option value="">Selecciona un departamento</option>
                <?php
                $departamentos = pg_query($conexion, "SELECT * FROM departamento");
                while ($depto = pg_fetch_assoc($departamentos)) {
                    echo "<option value='{$depto['id_departamento']}'>{$depto['nombre_departamento']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre de Usuario</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Guardar Persona</button>
    </form>
</div>
<br><br><br><br><br><br>

<?php
include_once('footer.php');?>