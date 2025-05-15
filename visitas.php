<?php
session_start();
include_once('header.php');
include("configuracion/conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_persona'])) {
    header("Location: login.php");
    exit();
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_institucion = $_POST['id_centro_educativo'];
    $codigo_institucion = $_POST['codigo_institucion'];
    $grado = $_POST['grado_seccion_turno'];
    $seccion = $_POST['grado_seccion_turno'];
    $turno = $_POST['turno'];
    $cantidad_estudiantes = $_POST['numero_estudiantes'];
    $fecha_acompanamiento = $_POST['fecha_acompanamiento'];
    $codigo_persona = $_SESSION['id_persona'];

    // Iniciar transacción
    pg_query($conexion, "BEGIN");
    $transaccion_exitosa = true; // Variable para controlar el estado de la transacción

    $query_insert_respuesta = "INSERT INTO respuestas (id_institucion, codigo_persona, grado, seccion, turno, cantidad_estudiantes, fecha) 
                               VALUES ($id_institucion, $codigo_persona, '$grado', '$seccion', '$turno', $cantidad_estudiantes, '$fecha_acompanamiento') RETURNING cod_respuesta";

    $resultado_insert_respuesta = pg_query($conexion, $query_insert_respuesta);
    if ($resultado_insert_respuesta) {
        $mensaje = "Los datos generales se han guardado correctamente.";
        $row = pg_fetch_row($resultado_insert_respuesta);
        $id_respuesta = $row[0];
    } else {
        $mensaje = "Error al guardar los datos generales: " . pg_last_error($conexion) . "<br>";
        $mensaje .= "Query: " . htmlspecialchars($query_insert_respuesta) . "<br>";
        echo "<b>Error:</b> " . htmlspecialchars($mensaje) . "<br>";
        $transaccion_exitosa = false; // Indicar que la transacción falló
        pg_query($conexion, "ROLLBACK");
        exit();
    }

    // Verificar que se obtuvo un ID válido antes de proceder
    if ($transaccion_exitosa && (!isset($id_respuesta) || !is_numeric($id_respuesta) || $id_respuesta <= 0)) { // Verificar también $transaccion_exitosa
        $mensaje = "Error: No se pudo obtener un ID de respuesta válido.";
        echo "<b>Error:</b> " . htmlspecialchars($mensaje) . "<br>";
        $transaccion_exitosa = false;
        pg_query($conexion, "ROLLBACK");
        exit();
    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'respuesta_') === 0) {
            $cod_pregunta = str_replace('respuesta_', '', $key);
            $respuesta = $value;
            $comentario = $_POST['comentario_' . $cod_pregunta];
            $query_insert_detalles = "INSERT INTO respuestas_detalladas (respuestas_cod_respuesta, cod_pregunta, respuesta, comentario) 
                                              VALUES ($id_respuesta, $cod_pregunta, '$respuesta', '$comentario')";
            $resultado_insert_detalles = pg_query($conexion, $query_insert_detalles);
            if (!$resultado_insert_detalles) {
                $mensaje .= "<br>Error al guardar la respuesta a la pregunta $cod_pregunta: " . pg_last_error($conexion) . "<br>";
                $mensaje .= "Query: " . htmlspecialchars($query_insert_detalles) . "<br>";
                $transaccion_exitosa = false; // Indicar que la transacción falló
                pg_query($conexion, "ROLLBACK");
                break;
            }
        }
    }
    if ($transaccion_exitosa) {
        pg_query($conexion, "COMMIT");
        $mensaje .= "<br>Las respuestas se han guardado correctamente.";
    } else {
        pg_query($conexion, "ROLLBACK");
        $mensaje .= "<br>Error al guardar las respuestas.";
    }
}

// Consulta para obtener todas las categorías únicas
$query_categorias = "SELECT DISTINCT categoria FROM preguntas ORDER BY categoria";
$resultado_categorias = pg_query($conexion, $query_categorias);
$categorias = pg_fetch_all($resultado_categorias);

// Consulta para obtener todas las preguntas
$query_preguntas = "SELECT * FROM preguntas ORDER BY categoria, cod_pregunta";
$resultado_preguntas = pg_query($conexion, $query_preguntas);
$preguntas = pg_fetch_all($resultado_preguntas);
?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Formulario de visitas</h2>

    <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-info text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
        <h3 class="mt-4 mb-3">Datos del Centro Educativo</h3>
        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento del centro educativo</label>
            <select class="form-select" id="departamento" name="departamento" required>
                <?php
                $query_departamentos = "SELECT * FROM departamento";
                $resultado_departamentos = pg_query($conexion, $query_departamentos);
                while ($fila_departamento = pg_fetch_assoc($resultado_departamentos)) {
                    echo "<option value='" . $fila_departamento['id_departamento'] . "'>" . $fila_departamento['nombre_departamento'] . "</option>";
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
            <label for="grado_seccion_turno" class="form-label">Grado, Sección y Turno</label>
            <input type="text" class="form-control" id="grado_seccion_turno" name="grado_seccion_turno" required>
        </div>
        <div class = "mb-3">
            <label for = "turno" class = "form-label">Turno</label>
            <input type = "text" class = "form-control" id = "turno" name = "turno" required>
        </div>

        <div class="mb-3">
            <label for="numero_estudiantes" class="form-label">Número de estudiantes</label>
            <input type="number" class="form-control" id="numero_estudiantes" name="numero_estudiantes" required min="1">
        </div>

        <div class="mb-3">
            <label for="fecha_acompanamiento" class="form-label">Fecha de acompañamiento</label>
            <input type="date" class="form-control" id="fecha_acompanamiento" name="fecha_acompanamiento" required>
        </div>

        <?php if ($categorias && $preguntas) : ?>
            <?php foreach ($categorias as $categoria_item) : ?>
                <h3 class="mt-4 mb-3"><?php echo htmlspecialchars($categoria_item['categoria']); ?></h3>
                <?php foreach ($preguntas as $pregunta_item) : ?>
                    <?php if ($pregunta_item['categoria'] === $categoria_item['categoria']) : ?>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars($pregunta_item['pregunta']); ?></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>" id="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>_si" value="si" required>
                                <label class="form-check-label" for="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>_si">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>" id="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>_no" value="no">
                                <label class="form-check-label" for="respuesta_<? echo $pregunta_item['cod_pregunta']; ?>_no">No</label>
                            </div>
                            <textarea class="form-control mt-2" name="comentario_<?php echo $pregunta_item['cod_pregunta']; ?>" placeholder="Comentario"></textarea>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-warning text-center">No se encontraron preguntas en la base de datos.</div>
        <?php endif; ?>

<<<<<<< HEAD
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
                <label for="grado" class="form-label">Grado</label>
                <input type="text" class="form-control" id="grado" name="grado" required>
            </div>

            <div class="mb-3">
                <label for="seccion" class="form-label">Sección</label>
                <input type="text" class="form-control" id="seccion" name="seccion" required>
            </div>

            <div class="mb-3">
                <label for="turno" class="form-label">Turno</label>
                <select class="form-select" id="turno" name="turno" required>
                    <option value="">Seleccione un turno</option>
                    <option value="Mañana">Mañana</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noche">Noche</option>
                    <option value="Único">Único</option>
                </select>
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
=======
        <button type="submit" class="btn btn-primary w-100 mt-4">Guardar Persona</button>
    </form>
</div>

<script>
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');
    const distritoSelect = document.getElementById('distrito');
    const centroEducativoSelect = document.getElementById('centro_educativo');
    const codigoInstitucionInput = document.getElementById('codigo_institucion');
>>>>>>> 5a64c64b8c32f6b5838a00e2ceacaf30b61c31b0

                // Cargar municipios al cambiar el departamento
                departamentoSelect.addEventListener('change', function() {
                    const departamentoId = this.value;

<<<<<<< HEAD
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
=======
        municipioSelect.disabled = true;
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        distritoSelect.disabled = true;
        distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';
         codigoInstitucionInput.value = '';

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
        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';
         codigoInstitucionInput.value = '';

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

        centroEducativoSelect.disabled = true;
        centroEducativoSelect.innerHTML = '<option value="">Seleccione un centro educativo</option>';
         codigoInstitucionInput.value = '';

        if (distritoId) {
            fetch('obtener_centros_educativos.php?distrito_id=' + distritoId)
                .then(response => response.json())
                .then(data => {
                    centroEducativoSelect.disabled = false;
                    data.forEach(centro => {
                        const option = document.createElement('option');
                        option.value = centro.id_institucion;
                        option.textContent = centro.nombre_institucion;
                        option.dataset.codigo = centro.codigo_de_infraestructura;
                        centroEducativoSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al obtener los centros educativos:', error);
                });
        }
    });

    // Evento para completar el código al seleccionar un centro educativo
    centroEducativoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const codigo = selectedOption.dataset.codigo;
        codigoInstitucionInput.value = codigo || '';
    });
</script>

<?php
include_once('footer.php');
?>
>>>>>>> 5a64c64b8c32f6b5838a00e2ceacaf30b61c31b0
