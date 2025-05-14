<?php
require('fpdf/fpdf.php');
require_once 'conexion.php'; //conexión a PostgreSQL

// Consulta general de personas registradas
$sql = "
    SELECT p.id_persona, p.nombre, p.apellido, r.nombre_rol, i.nombre_institucion, d.nombre_departamento, dis.nombre_distrito
    FROM persona p
    LEFT JOIN rol r ON p.id_rol = r.id_rol
    LEFT JOIN institucion i ON p.id_institucion = i.id_institucion
    LEFT JOIN distrito dis ON p.id_distrito = dis.id_distrito
    LEFT JOIN departamento d ON dis.id_departamento = d.id_departamento
    ORDER BY p.nombre ASC;
";
$result = pg_query($conexion, $sql);

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('Reporte General de Personas Registradas'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    function TablaPersonas($data) {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(25, 8, 'ID', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Nombre', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Apellido', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Rol', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Institucion', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Departamento', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Distrito', 1, 1, 'C', true);

        $this->SetFont('Arial', '', 9);
        foreach ($data as $row) {
            $this->Cell(25, 7, $row['id_persona'], 1);
            $this->Cell(45, 7, utf8_decode($row['nombre']), 1);
            $this->Cell(45, 7, utf8_decode($row['apellido']), 1);
            $this->Cell(30, 7, utf8_decode($row['nombre_rol']), 1);
            $this->Cell(40, 7, utf8_decode($row['nombre_institucion']), 1);
            $this->Cell(30, 7, utf8_decode($row['nombre_departamento']), 1);
            $this->Cell(30, 7, utf8_decode($row['nombre_distrito']), 1);
            $this->Ln();
        }
    }
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

$pdf = new PDF();
$pdf->AddPage('L');
$pdf->TablaPersonas($data);
$pdf->Output('I', 'reporte_personas.pdf');
?>
