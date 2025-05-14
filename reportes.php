<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once('header.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">📄 Reportes Disponibles</h2>

        <div class="list-group">
            <a href="reporte_personas.php" class="list-group-item list-group-item-action">
                👤 Reporte de Personas
            </a>
            <a href="reporte_instituciones.php" class="list-group-item list-group-item-action">
                🏫 Reporte de Instituciones
            </a>
            <!-- Agrega más enlaces aquí si creas más reportes -->
        </div>
    </div>

    <?php include_once('footer.php'); ?>
</body>
</html>
