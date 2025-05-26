<?php
// Puedes incluir aquí la lógica de sesión o cualquier PHP que necesites al inicio
// Por ejemplo, si este es un archivo incluido en index.php, no necesitará session_start()
// si ya se inició en index.php o en un archivo de cabecera como header.php.

// Si este es un archivo de página completo e independiente, deberías incluir el header y el nav.
// Por ejemplo:
// include 'header.php'; // o nav.php si tu estructura de página lo requiere
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema de Control de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    // Aquí puedes incluir tu barra de navegación si 'inicio.php' es una página completa
    // Por ejemplo:
    // include 'nav.php'; // Si tienes un archivo nav.php que incluye la barra de navegación
    ?>

    <section id="inicio" class="position-relative">
        <div class="bg-image position-relative" style="height: 500px; background-image: url('./recursos/images.jpg'); background-size: cover; background-position: center;">
            <div class="position-absolute w-100 h-100" style="background-color: rgba(0,20,60,0.7);"></div>
            <div class="container position-relative h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-8 text-white">
                        <h1 class="display-4 fw-bold mb-4">Sistema de Control de Visitas a Instituciones Educativas</h1>
                        <p class="lead mb-4">Plataforma diseñada para mejorar la supervisión y seguimiento de visitas a centros educativos en todo El Salvador, garantizando la calidad y transparencia del sistema educativo nacional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Visitas Realizadas</h5>
                            <h1 class="display-4">30</h1>
                            <p class="text-muted">En los últimos 3 meses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Instituciones Registradas</h5>
                            <h1 class="display-4">6000</h1>
                            <p class="text-muted">A nivel nacional</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-dark">Supervisores Activos</h5>
                            <h1 class="display-4">5</h1>
                            <p class="text-muted">En todo el país</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'footer.php' ?>
</body>
</html>