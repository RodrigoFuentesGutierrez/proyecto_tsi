<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=informeAsistencia.xls');

include '../php/conexionBD.php';

$sql = "SELECT fecha, SUM(asistencia) AS total_asistencia FROM asistencia GROUP BY fecha";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Asistencia</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black; /* Borde de las celdas */
            padding: 8px;
            text-align: center; /* Centrado del contenido */
        }
        th {
            background-color: #1E90FF; /* Color de fondo para encabezados */
            color: white; /* Color del texto para encabezados */
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Informe de Asistencia</h2>
    <div class="container">
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Total de Asistencia</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php
$conn->close();
?>

