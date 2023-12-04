<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$alumno_id = $_SESSION['user']['id'];

// Connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid";

// Obtener fechas de inicio y fin desde GET o usar valores por defecto
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : date('Y-m-01');
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-t');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch grupo del alumno
    $stmtGrupo = $conn->prepare("SELECT g.id, g.nombre_grupo FROM grupos g JOIN alumnos_grupos ag ON g.id = ag.grupo_id WHERE ag.alumno_id = :alumno_id");
    $stmtGrupo->bindParam(':alumno_id', $alumno_id, PDO::PARAM_INT);
    $stmtGrupo->execute();
    $grupo = $stmtGrupo->fetch();

    // Fetch asistencias del alumno para el rango de fechas
    $stmtAsistencias = $conn->prepare("SELECT DATE(fecha) as fecha, asistio FROM asistencias WHERE uid = :uid AND DATE(fecha) BETWEEN :fechaInicio AND :fechaFin");
    $stmtAsistencias->bindParam(':uid', $_SESSION['user']['uid']);
    $stmtAsistencias->bindParam(':fechaInicio', $fechaInicio);
    $stmtAsistencias->bindParam(':fechaFin', $fechaFin);
    $stmtAsistencias->execute();
    $asistencias = $stmtAsistencias->fetchAll();

    // Convertir asistencias en un formato más manejable
    $asistenciasPorFecha = [];
    foreach ($asistencias as $asistencia) {
        $fecha = $asistencia['fecha'];
        $asistenciasPorFecha[$fecha] = $asistencia['asistio'];
    }

    // Calcular el total de asistencias e inasistencias
    $totalAsistencias = 0;
    $totalInasistencias = 0;
    foreach ($asistenciasPorFecha as $asistio) {
        if ($asistio == 0) {
            $totalAsistencias++;
        } else {
            $totalInasistencias++;
        }
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>

<?php include('nav_alumno.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Alumno</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir librería para gráficos, por ejemplo, Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Mis Asistencias</h1>

    <a href="logout.php">Cerrar sesion</a>
    
    <div class="alert alert-primary" role="alert">
        <?php
        $user = $_SESSION['user'];
        echo "Bienvenido, " . $user['nombre'];
        ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Asistencias</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalAsistencias; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Inasistencias</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $totalInasistencias; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <form action="" method="get">
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="fechaInicio">Fecha de inicio:</label>
                <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo $fechaInicio; ?>">
            </div>
            <div class="col-md-6">
                <label for="fechaFin">Fecha de fin:</label>
                <input type="date" id="fechaFin" name="fechaFin" value="<?php echo $fechaFin; ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <!-- Sección de gráficos -->
    <div class="row">
        <div class="col-md-6">
            <canvas id="graficoAsistencias"></canvas>
        </div>
    </div>

    <h3>Asistencia del rango seleccionado - Grupo: <?php echo $grupo['nombre_grupo']; ?></h3>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Asistencia</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $start = new DateTime($fechaInicio);
        $end = (new DateTime($fechaFin))->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $date) {
            if ($date->format('N') < 6) {  // Solo días de semana
                echo "<tr>";
                echo "<td>" . $date->format('d-m-Y') . "</td>";
                if (isset($asistenciasPorFecha[$date->format('Y-m-d')])) {
                    if ($asistenciasPorFecha[$date->format('Y-m-d')] == 0) {
                        echo "<td class='table-success'>Asistió</td>";
                    } else {
                        echo "<td class='table-danger'>No Asistió</td>";
                    }
                } else {
                    echo "<td></td>";  // Celda vacía si no hay registro
                }
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
</div>

<script>
    // Código para el gráfico
    var ctx = document.getElementById('graficoAsistencias').getContext('2d');
    var graficoAsistencias = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Asistencias', 'Inasistencias'],
            datasets: [{
                label: '# de Asistencias/Inasistencias',
                data: [<?php echo $totalAsistencias; ?>, <?php echo $totalInasistencias; ?>],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>