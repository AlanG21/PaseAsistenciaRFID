<?php
session_start();

// Connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // You should hash and then compare!

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, nombre, apellido, email, password, 'profesor' as tipo, NULL as uid FROM profesores WHERE email=?
    UNION
    SELECT id, nombre, apellido, email, password, 'administrador' as tipo, NULL as uid FROM administradores WHERE email=?
    UNION
    SELECT id, nombre, apellido, email, password, 'alumno' as tipo, CAST(uid AS CHAR) as uid FROM alumnos WHERE email=?");
    
    $stmt->bind_param("sss", $email, $email, $email);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $rol = $user['tipo'];
        $_SESSION['user'] = $user;

        if ($rol == 'administrador') {
            header('Location: dashboard_administrador.php');
        } elseif ($rol == 'profesor') {
            header('Location: dashboard_profesor.php');
        } elseif ($rol == 'alumno') {
            header('Location: dashboard_alumno.php');
        }
    } 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            margin-top: 5%;
            margin-bottom: 5%;
        }
        .login-form-1 {
            padding: 9%;
            background:#282726;
            box-shadow: 0px 0px 8px #888888;
        }
        .login-form-1 h3 {
            text-align: center;
            color: #fff;
        }
        .btnSubmit {
            font-weight: 600;
            width: 50%;
            color: #282726;
            background-color: #F3F3F3;
            border: none;
            border-radius: 1.5rem;
            padding: 2%;
        }
        .login-form-1 .btnSubmit:hover {
            color: #fff;
            background-color: #0062cc;
        }
        .login-form-1 .forget-password {
            color: #fff;
            font-weight: 600;
            text-align: center;
            display: block;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="row">
        <div class="col-md-6 offset-md-3 login-form-1">
            <h3>Iniciar Sesión</h3>
            <form action="login.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Tu Email *" value="" required />
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Tu Contraseña *" value="" required />
                </div>
                <div class="form-group">
                    <input type="submit" class="btnSubmit" value="Iniciar Sesión" />
                </div>
                <div class="form-group">
                    <a href="#" class="forget-password">Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
