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

    $query = "INSERT INTO persona (
        id_rol, codigo_persona, correo_persona, clave_persona,
        nombre_persona, apellido_persona, documento_de_identificacion,
        id_distrito_reside, id_departamento_labora
    ) VALUES (
        $id_rol, '$codigo_persona', '$correo_persona', '$clave_persona',
        '$nombre_persona', '$apellido_persona', '$documento_de_identificacion',
        $id_distrito_reside, $id_departamento_labora
    )";

    $resultado = pg_query($conexion, $query);

    if ($resultado) {
        $mensaje = "✅ Persona insertada correctamente.";
    } else {
        $mensaje = "❌ Error al insertar: " . pg_last_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario Persona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Formulario de Registro de Persona</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info text-center">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
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
            <label class="form-label">Documento de Identificación (DUI)</label>
            <input type="text" class="form-control" name="documento_de_identificacion" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Rol</label>
            <select class="form-select" name="id_rol" required>
                <option value="">-- Selecciona un rol --</option>
                <option value="1">Administrador</option>
                <option value="2">Editor</option>
                <option value="3">Visor</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Distrito de Residencia</label>
            <select class="form-select" name="id_distrito_reside" required>
                <option value="">-- Selecciona un distrito --</option>
                <?php 
                $query = "SELECT * FROM distrito";
                $resultado = pg_query($conexion, $query);
                while ($fila = pg_fetch_assoc($resultado)) {
                    echo "<option value='" . $fila['id_distrito'] . "'>" . $fila['nombre_distrito'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">ID Departamento</label>
            <input type="number" class="form-control" name="id_departamento_labora" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Guardar Persona</button>
    </form>
</div>
</body>
</html>