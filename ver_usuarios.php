<?php

include("configuracion/conexion.php");

// Consulta SQL modificada con JOINs
$query = "SELECT
    p.id_persona,
    p.username,
    p.clave_persona,
    p.id_rol,
    p.nombre_persona,
    p.apellido_persona,
    p.correo_persona,
    p.documento_de_identificacion,
    dtr.nombre_distrito AS nombre_distrito_reside,
    dp.nombre_departamento AS nombre_departamento_labora,
    p.codigo_persona
FROM
    public.persona p
LEFT JOIN
    public.distrito dtr ON p.id_distrito_reside = dtr.id_distrito
LEFT JOIN
    public.departamento dp ON p.id_departamento_labora = dp.id_departamento
ORDER BY
    p.id_persona ASC";
$resultado = pg_query($conexion, $query);

// Verificar si hubo error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error());
}
include_once("header.php");
?>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Listado de Personas</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Clave</th>
                        <th>Rol ID</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Documento</th>
                        <th>Distrito Residencia</th>
                        <th>Departamento Laboral</th>
                        <th>Código Persona</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['id_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['username']); ?></td>
                            <td><?php echo htmlspecialchars($fila['clave_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['id_rol']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['apellido_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['correo_persona']); ?></td>
                            <td><?php echo htmlspecialchars($fila['documento_de_identificacion']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_distrito_reside']); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre_departamento_labora']); ?></td>
                            <td><?php echo htmlspecialchars($fila['codigo_persona']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include_once("footer.php") ?>