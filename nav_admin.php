<!-- nav.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Simple navigation bar styling */
        .navbar {
            background-color: #0180C8;
            overflow: hidden;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #0583CB;
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard_administrador.php">Dashboard</a>
        <a href="registrar_alumnos.php">Registro alumnos</a>
        <a href="registrar_profesores.php">Registo profesores</a>
        <a href="agregar_grupo.php">Agregar grupos</a>
        <a href="registrar_alumno.php">Agregar alumno a grupo</a>
        <a href="crear_horario.php">Hacer horario del profesor</a>
        <a href="#" onclick="confirmLogout()">Cerrar sesion</a>

    </div>
</body>
<script>
    function confirmLogout() {
        Swal.fire({
            title: '¿Estás seguro de que quieres cerrar sesión?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        })
    }
</script>

</html>
