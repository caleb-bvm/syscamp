<?php include_once("header.php"); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Generación de Reportes</h2>

    <div class="card p-4 shadow-sm">
        <h5 class="mb-3">Reporte general de personas registradas</h5>
        <p>Descarga un informe PDF con los datos actuales de las personas registradas en el sistema.</p>
        <a href="reportes/reporte_personas.php" class="btn btn-primary" target="_blank">
            <i class="bi bi-file-earmark-pdf-fill"></i> Generar PDF
        </a>
    </div>
</div>

<?php include_once("footer.php"); ?>
