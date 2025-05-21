<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - SEC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --ministerio-azul: #0a3d62; /* Color azul marino tipo Ministerio */
            --ministerio-azul-claro: #1e5b8c;
        }
        
        .bg-ministerio {
            background-color: var(--ministerio-azul) !important;
        }
        
        .text-ministerio {
            color: var(--ministerio-azul) !important;
        }
        
        .btn-ministerio {
            background-color: var(--ministerio-azul);
            border-color: var(--ministerio-azul);
            color: white;
        }
        
        .btn-ministerio:hover {
            background-color: var(--ministerio-azul-claro);
            border-color: var(--ministerio-azul-claro);
            color: white;
        }
        
        .seccion { 
            display: none; 
        }
        
        .seccion.active { 
            display: block; 
        }
        
        /* Estilos responsivos para menú móvil */
        @media (max-width: 991px) {
            .navbar-collapse .nav-item.d-none {
                display: block !important;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->

<!-- CONTENIDO -->
 <main class='container-fluid p-3' style='min-height: calc( 100vh - 70px - 40px)'> 
        <?php include 'inicio.php' ?>
 </main>



</body>
</html>