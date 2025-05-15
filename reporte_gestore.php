<?php
// Incluir el archivo de configuración de TCPDF
require_once('tcpdf/tcpdf_config.php');

// Incluir la librería principal de TCPDF
require_once('tcpdf/tcpdf.php');

include("configuracion/conexion.php");

// Crear nuevo objeto PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre o Nombre de la Empresa');
$pdf->SetTitle('Reporte de Usuarios');
$pdf->SetSubject('Lista de Usuarios');
$pdf->SetKeywords('TCPDF, PDF, usuarios, reporte');

// Márgenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Fuente por defecto
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Auto salto de página
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer ratio de escalado de imagen
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Añadir una página
$pdf->AddPage();

// Contenido del PDF
$html = '<h1>Reporte de Usuarios</h1>';
$html .= '<table border="1">';
$html .= '<thead>';
$html .= '<tr>';
$html .= '<th>Nombre de Usuario</th>';
$html .= '<th>Correo Electrónico</th>';
$html .= '<th>Nombres</th>';
$html .= '<th>Apellidos</th>';
$html .= '<th>Rol</th>';
$html .= '<th>Distrito Laboral</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

// Consulta para obtener los datos de los usuarios
$query = "SELECT u.username, u.correo_personas, u.nombre_personas, u.apellido_persona, r.nombre_rol, d.nombre_distrito
          FROM public.usuarios u
          INNER JOIN public.roles r ON u.id_rol = r.id_rol
          INNER JOIN public.distrito d ON u.id_distrito_reside = d.id_distrito"; // Ajusta los nombres de las tablas y columnas según tu esquema

$resultado = pg_query($conexion, $query);

if ($resultado) {
    while ($fila = pg_fetch_assoc($resultado)) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($fila['username']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['correo_personas']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['nombre_personas']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['apellido_persona']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['nombre_rol']) . '</td>';
        $html .= '<td>' . htmlspecialchars($fila['nombre_distrito']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="6">Error al obtener los datos de los usuarios: ' . pg_last_error($conexion) . '</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Imprimir el texto usando writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Cerrar y generar el PDF
$pdf->Output('reporte_de_usuarios.pdf', 'I'); // 'I' para mostrar en el navegador, 'D' para forzar descarga
?>