<?php
include_once('header.php');
include_once('configuracion/conexion.php'); // Asegúrate de que esta ruta sea correcta y conecte a tu DB
?>

<div class="container mt-4">
    <h2 class="fs-2 mb-3">Dashboard General</h2>

    <?php
    $totalVisitas = 0;
    $totalPreguntasRespondidas = 0;
    $totalInstitucionesVisitadas = 0;
    $tasaCompletitud = 0;

    // 1. Total de Visitas
    $queryVisitas = "SELECT COUNT(*) AS total_visitas FROM respuestas";
    $resultVisitas = pg_query($conexion, $queryVisitas);
    if ($resultVisitas && pg_num_rows($resultVisitas) > 0) {
        $rowVisitas = pg_fetch_assoc($resultVisitas);
        $totalVisitas = $rowVisitas['total_visitas'];
    } else {
        error_log("Error al obtener total de visitas: " . pg_last_error($conexion));
    }

    // 2. Total de Preguntas Respondidas
    $queryPreguntasRespondidas = "SELECT COUNT(*) AS total_preguntas_respondidas FROM respuestas_detalladas";
    $resultPreguntasRespondidas = pg_query($conexion, $queryPreguntasRespondidas);
    if ($resultPreguntasRespondidas && pg_num_rows($resultPreguntasRespondidas) > 0) {
        $rowPreguntasRespondidas = pg_fetch_assoc($resultPreguntasRespondidas);
        $totalPreguntasRespondidas = $rowPreguntasRespondidas['total_preguntas_respondidas'];
    } else {
        error_log("Error al obtener total de preguntas respondidas: " . pg_last_error($conexion));
    }

    // 3. Total de Instituciones Visitadas (distintas)
    $queryInstituciones = "SELECT COUNT(DISTINCT id_institucion) AS total_instituciones FROM respuestas";
    $resultInstituciones = pg_query($conexion, $queryInstituciones);
    if ($resultInstituciones && pg_num_rows($resultInstituciones) > 0) {
        $rowInstituciones = pg_fetch_assoc($resultInstituciones);
        $totalInstitucionesVisitadas = $rowInstituciones['total_instituciones'];
    } else {
        error_log("Error al obtener total de instituciones visitadas: " . pg_last_error($conexion));
    }

    // 4. Tasa de Completitud (ejemplo básico, ajustar según tu lógica de negocio)
    $totalPreguntasSistema = 0;
    $queryTotalPreguntas = "SELECT COUNT(*) AS total_preguntas_sistema FROM preguntas";
    $resultTotalPreguntas = pg_query($conexion, $queryTotalPreguntas);
    if ($resultTotalPreguntas && pg_num_rows($resultTotalPreguntas) > 0) {
        $rowTotalPreguntas = pg_fetch_assoc($resultTotalPreguntas);
        $totalPreguntasSistema = $rowTotalPreguntas['total_preguntas_sistema'];
    } else {
        error_log("Error al obtener total de preguntas en el sistema: " . pg_last_error($conexion));
    }

    if ($totalVisitas > 0 && $totalPreguntasSistema > 0) {
        $tasaCompletitud = ($totalPreguntasRespondidas / ($totalVisitas * $totalPreguntasSistema)) * 100;
        $tasaCompletitud = round($tasaCompletitud, 2);
    } else {
        $tasaCompletitud = 0;
    }

    // Últimas 5 Visitas Recientes
    $queryVisitasRecientes = "
        SELECT r.fecha, i.nombre_institucion, r.grado, p.username
        FROM respuestas r
        JOIN institucion i ON r.id_institucion = i.id_institucion
        JOIN persona p ON r.codigo_persona = p.id_persona
        ORDER BY r.fecha DESC
        LIMIT 5
    ";
    $resultVisitasRecientes = pg_query($conexion, $queryVisitasRecientes);
    $visitasRecientes = [];
    if ($resultVisitasRecientes) {
        while ($row = pg_fetch_assoc($resultVisitasRecientes)) {
            $visitasRecientes[] = $row;
        }
    } else {
        error_log("Error al cargar visitas recientes: " . pg_last_error($conexion));
    }
    ?>

    <div class="row g-3 my-2">
        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2 text-dark"><?= $totalVisitas ?></h3>
                    <p class="fs-5 text-secondary">Visitas a Centros Educativos</p>
                </div>
                <i class="bi bi-calendar-check report-icon border rounded-full secondary-bg p-3"></i> </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2 text-dark"><?= $totalPreguntasRespondidas ?></h3>
                    <p class="fs-5 text-secondary">Preguntas Respondidas</p>
                </div>
                <i class="bi bi-question-lg report-icon border rounded-full secondary-bg p-3"></i> </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2 text-dark"><?= $totalInstitucionesVisitadas ?></h3>
                    <p class="fs-5 text-secondary">Instituciones Visitadas</p>
                </div>
                <i class="bi bi-building report-icon border rounded-full secondary-bg p-3"></i> </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2 text-dark"><?= $tasaCompletitud ?>%</h3>
                    <p class="fs-5 text-secondary">Tasa de Completitud</p>
                </div>
                <i class="bi bi-graph-up-arrow report-icon border rounded-full secondary-bg p-3"></i> </div>
        </div>
    </div>

    <div class="row my-5">
        <h3 class="fs-4 mb-3">Visitas Recientes</h3>
        <div class="col-12">
            <div class="table-responsive p-4 shadow-sm bg-white rounded">
                <?php if (!empty($visitasRecientes)): ?>
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Institución</th>
                            <th>Grado</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitasRecientes as $visita): ?>
                        <tr class="text-dark">
                            <td><?= htmlspecialchars($visita['fecha']) ?></td>
                            <td><?= htmlspecialchars($visita['nombre_institucion']) ?></td>
                            <td><?= htmlspecialchars($visita['grado']) ?></td>
                            <td><?= htmlspecialchars($visita['username']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-center text-muted">No se encontraron visitas recientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div> <?php include_once('footer.php'); ?>