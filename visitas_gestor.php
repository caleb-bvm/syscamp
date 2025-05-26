<?php
include_once('navuser.php');
include("configuracion/conexion.php");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_persona'])) {
    header("Location: login.php");
    exit();
}

// Define la carpeta donde se guardarán las imágenes de comprobación.
// ¡MUY IMPORTANTE! Asegúrate de que esta carpeta exista y tenga permisos de escritura para el servidor web.
$directorio_subidas = 'imagenes_evidencia_visita/'; // Directorio específico para evidencias

// Crea la carpeta si no existe
if (!is_dir($directorio_subidas)) {
    mkdir($directorio_subidas, 0777, true); // Permisos completos (para prueba), ajustar en producción a 0755
}

// Inicializar mensaje vacío
$mensaje = "";

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_institucion = $_POST['id_centro_educativo'];
    // $codigo_institucion = $_POST['codigo_institucion']; // Ya no se usa directamente aquí, se obtiene del select
    $grado = $_POST['grado_seccion_turno'];
    $seccion = $_POST['grado_seccion_turno'];
    $turno = $_POST['turno'];
    $cantidad_estudiantes = $_POST['numero_estudiantes'];
    $fecha_acompanamiento = $_POST['fecha_acompanamiento'];
    $codigo_persona = $_SESSION['id_persona'];
    $nombre_director = $_POST['nombre_director'];

    // --- Lógica para la subida de la imagen de comprobación ---
    $ruta_imagen_guardada = null; // Inicializar a null

    // El input file se llama 'imagen_evidencia'
    if (isset($_FILES['imagen_evidencia']) && $_FILES['imagen_evidencia']['error'] === UPLOAD_ERR_OK) {
        $nombre_original = basename($_FILES['imagen_evidencia']['name']);
        $tipo_archivo = $_FILES['imagen_evidencia']['type'];
        $tamano_archivo = $_FILES['imagen_evidencia']['size'];
        $nombre_temporal = $_FILES['imagen_evidencia']['tmp_name'];

        // Generar un nombre único para el archivo
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_unico = uniqid('evidencia_') . '.' . $extension; // Prefijo para identificar
        $ruta_destino = $directorio_subidas . $nombre_unico;

        // Validaciones básicas de tipo y tamaño
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg']; // Añadido 'image/jpg' explícitamente
        $tamano_maximo = 5 * 1024 * 1024; // 5 MB

        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            $mensaje .= "<br>Error: Tipo de archivo de imagen de evidencia no permitido. Solo JPG, PNG, GIF.";
        } elseif ($tamano_archivo > $tamano_maximo) {
            $mensaje .= "<br>Error: El archivo de imagen de evidencia es demasiado grande (máximo 5 MB).";
        } else {
            if (move_uploaded_file($nombre_temporal, $ruta_destino)) {
                $ruta_imagen_guardada = $ruta_destino;
                $mensaje .= "<br>La imagen de evidencia se ha subido correctamente.";
            } else {
                $mensaje .= "<br>Error al mover el archivo de imagen de evidencia subido.";
            }
        }
    } elseif (isset($_FILES['imagen_evidencia']) && $_FILES['imagen_evidencia']['error'] !== UPLOAD_ERR_NO_FILE) {
        $mensaje .= "<br>Error en la subida de la imagen de evidencia: Código de error " . $_FILES['imagen_evidencia']['error'] . ".";
    }
    // --- Fin de la lógica de subida de imagen ---

    // Iniciar transacción
    pg_query($conexion, "BEGIN");
    $transaccion_exitosa = true;

    // Los datos generales de la respuesta, ¡incluyendo la ruta de la imagen adjunta!
    $escaped_ruta_imagen = $ruta_imagen_guardada ? "'" . pg_escape_string($conexion, $ruta_imagen_guardada) . "'" : "NULL";

    $query_insert_respuesta = "INSERT INTO respuestas (id_institucion, codigo_persona, grado, seccion, turno, cantidad_estudiantes, fecha, director, ruta_imagen_adjunta)
                               VALUES ($id_institucion, $codigo_persona, '" . pg_escape_string($conexion, $grado) . "', '" . pg_escape_string($conexion, $seccion) . "', '" . pg_escape_string($conexion, $turno) . "', $cantidad_estudiantes, '" . pg_escape_string($conexion, $fecha_acompanamiento) . "', '" . pg_escape_string($conexion, $nombre_director) . "', $escaped_ruta_imagen) RETURNING cod_respuesta";

    $resultado_insert_respuesta = pg_query($conexion, $query_insert_respuesta);
    if ($resultado_insert_respuesta) {
        $mensaje .= "<br>Los datos generales de la visita se han guardado correctamente.";
        $row = pg_fetch_row($resultado_insert_respuesta);
        $id_respuesta = $row[0];
    } else {
        $mensaje .= "<br>Error al guardar los datos generales de la visita: " . pg_last_error($conexion) . "<br>";
        $mensaje .= "Query: " . htmlspecialchars($query_insert_respuesta) . "<br>";
        $transaccion_exitosa = false;
    }

    // Verificar que se obtuvo un ID válido antes de proceder
    if ($transaccion_exitosa && (!isset($id_respuesta) || !is_numeric($id_respuesta) || $id_respuesta <= 0)) {
        $mensaje .= "<br>Error: No se pudo obtener un ID de respuesta válido para los detalles.";
        $transaccion_exitosa = false;
    }

    // Procesar las respuestas detalladas (preguntas y comentarios)
    if ($transaccion_exitosa) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'respuesta_') === 0) {
                $cod_pregunta = str_replace('respuesta_', '', $key);
                $respuesta = pg_escape_string($conexion, $value);
                $comentario = pg_escape_string($conexion, $_POST['comentario_' . $cod_pregunta] ?? '');

                $query_insert_detalles = "INSERT INTO respuestas_detalladas (respuestas_cod_respuesta, cod_pregunta, respuesta, comentario)
                                         VALUES ($id_respuesta, $cod_pregunta, '$respuesta', '$comentario')";
                $resultado_insert_detalles = pg_query($conexion, $query_insert_detalles);

                if (!$resultado_insert_detalles) {
                    $mensaje .= "<br>Error al guardar la respuesta a la pregunta $cod_pregunta: " . pg_last_error($conexion) . "<br>";
                    $mensaje .= "Query: " . htmlspecialchars($query_insert_detalles) . "<br>";
                    $transaccion_exitosa = false;
                    break;
                }
            }
        }
    }

    if ($transaccion_exitosa) {
        pg_query($conexion, "COMMIT");
        $mensaje .= "<br>Todos los datos y la imagen de evidencia (si se adjuntó) se han guardado correctamente.";
    } else {
        pg_query($conexion, "ROLLBACK");
        $mensaje .= "<br>Error en alguna parte del proceso. Se revirtieron los cambios.";
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
    <h2 class="text-center mb-4">📄 Formulario de visitas</h2><br>

    <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-info text-center">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm" enctype="multipart/form-data">
        <h3 class="mt-4 mb-3">Datos del Centro Educativo</h3>
        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento del centro educativo</label>
            <select class="form-select" id="departamento" name="departamento" required>
                <?php
                $query_departamentos = "SELECT * FROM departamento";
                $resultado_departamentos = pg_query($conexion, $query_departamentos);
                while ($fila_departamento = pg_fetch_assoc($resultado_departamentos)) {
                    echo "<option value='" . $fila_departamento['id_departamento'] . "'>" . htmlspecialchars($fila_departamento['nombre_departamento']) . "</option>";
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
            <input type="text" class="form-control" id="codigo_institucion" name="codigo_institucion" required readonly>
        </div>

        <div class="mb-3">
            <label for="nombre_director" class="form-label">Nombre del Director</label>
            <input type="text" class="form-control" id="nombre_director" name="nombre_director" required>
        </div>

        <div class="mb-3">
            <label for="grado_seccion_turno" class="form-label">Grado, Sección</label>
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

        <div class="mb-3">
            <label for="imagen_evidencia" class="form-label">Subir Imagen de evidencia de la visita (Obligatorio)</label>
            <input type="file" class="form-control" id="imagen_evidencia" name="imagen_evidencia" accept="image/jpeg, image/png, image/gif, image/jpg" required>
            <small class="form-text text-muted">Sube una imagen como prueba de la visita (JPG, PNG, GIF). Tamaño máximo: 5 MB.</small>
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
                                <label class="form-check-label" for="respuesta_<?php echo $pregunta_item['cod_pregunta']; ?>_no">No</label>
                            </div>
                            <textarea class="form-control mt-2" name="comentario_<?php echo $pregunta_item['cod_pregunta']; ?>" placeholder="Comentario"></textarea>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-warning text-center">No se encontraron preguntas en la base de datos.</div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100 mt-4"><i class="bi bi-check-lg"></i> Guardar Visita</button>
    </form>
</div>

<script>
    const departamentoSelect = document.getElementById('departamento');
    const municipioSelect = document.getElementById('municipio');
    const distritoSelect = document.getElementById('distrito');
    const centroEducativoSelect = document.getElementById('centro_educativo');
    const codigoInstitucionInput = document.getElementById('codigo_institucion');

    // Cargar municipios al cambiar el departamento
    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;

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