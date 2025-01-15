<?php
include './PHP/Usuarios/check_access.php';

// Asegura que solo los usuarios con role_id 2 (editor) o 1 (admin) puedan acceder
checkAccess([1, 3]);
?>
<?php
include './PHP/db.php';

// Obtener las áreas existentes (nombre_efc) de la tabla 'personal'
$query = "SELECT DISTINCT nombre_efc FROM personal ORDER BY nombre_efc ASC";
$stmt = sqlsrv_query($conn, $query);
$areas = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $areas[] = $row['nombre_efc'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="icon" href="./IMG/logo.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        :root {
            --main-color: #ff9f00;
            --border-color: #ff6f00;
        }

        body {
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header img {
            max-width: 100px;
        }

        .form-header h2 {
            margin-top: 10px;
            color: var(--border-color);
        }

        .form-control {
            border-radius: 25px;
            border: 1px solid var(--border-color);
        }

        .btn-primary {
            background-color: var(--main-color);
            border-color: var(--border-color);
        }

        .btn-primary:hover {
            background-color: var(--border-color);
        }
        .btn-return {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 10px 15px;
            font-size: 16px;
        }
        .logout-button {
            position: fixed;
            bottom: 70px; /* Ajusta la distancia desde la parte inferior */
            right: 20px;  /* Ajusta la distancia desde la parte derecha */
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999; /* Asegúrate de que el botón esté sobre otros elementos */
        }

        .logout-button i {
            margin: 0;
        }

        .logout-button:hover {
            background-color: #ddd;
        }

        .logout-button:hover::after {
            content: "Cerrar sesión";
            position: absolute;
            bottom: 40px;
            right: 0;
            background-color: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 9999;
        }
    </style>
</head>

<body>
<button class="btn-return btn btn-primary" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
<div class="container">
<div class="form-header">
            <img src="./IMG/logo.png" alt="Logo">
            <h2>Registro de Empleado</h2>
        </div>
        <button id="syncButton" class="btn btn-primary btn-block mt-3">Sincronizar Datos</button>

    <form method="post" action="./PHP/insertar_personal.php">
        <div class="form-group">
            <label for="nombre_emp">Nombre:</label>
            <input type="text" class="form-control" id="nombre_emp" name="nombre_emp" required>
        </div>
        <div class="form-group">
            <label for="apellido_emp">Apellido:</label>
            <input type="text" class="form-control" id="apellido_emp" name="apellido_emp" required>
        </div>
        <div class="form-group">
            <label for="cedula_emp">Cédula:</label>
            <input type="text" class="form-control" id="cedula_emp" name="cedula_emp" required>
        </div>
        <div class="form-group">
            <label for="nombre_efc">Área:</label>
            <select class="form-control" id="nombre_efc" name="nombre_efc">
                <?php foreach ($areas as $area): ?>
                    <option value="<?= htmlspecialchars($area) ?>"><?= htmlspecialchars($area) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">Puede elegir un área existente o escribir una nueva.</small>
            <input type="text" class="form-control mt-2" id="nueva_efc" name="nueva_efc" placeholder="Escribir nueva área (opcional)">
        </div>
        <div class="form-group">
            <label for="nombre_cfg">Lugar:</label>
            <input type="text" class="form-control" id="nombre_cfg" name="nombre_cfg" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Registrar</button>
    </form>
</div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Manejo de alertas con SweetAlert2
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('success') === 'false') {
        const error = urlParams.get('error');
        let errorMessage = '';
        switch (error) {
            case 'cedula_existente':
                errorMessage = 'La cédula ya está registrada en el sistema.';
                break;
            case 'guardado_fallido':
                errorMessage = 'El registro no se pudo guardar. Inténtelo nuevamente.';
                break;
            default:
                errorMessage = 'Ha ocurrido un error desconocido.';
        }
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        }).then(() => {
            window.location.href = `ingreso_personal.php?cedula_temp=${urlParams.get('cedula_temp')}`;
        });
    } else if (urlParams.get('success') === 'true') {
        const cedulaTemp = urlParams.get('cedula_temp');
        Swal.fire({
            icon: 'success',
            title: 'Registro Exitoso',
            text: `El empleado con cédula ${cedulaTemp} ha sido registrado correctamente.`,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
        }).then(() => {
            window.location.href = 'ingreso_personal.php';
        });
    }
</script>
<script>
    // Función para convertir texto a mayúsculas automáticamente
    function convertirMayusculas(event) {
        event.target.value = event.target.value.toUpperCase();
    }

    // Selecciona los campos del formulario
    const nombreInput = document.getElementById('nombre_emp');
    const apellidoInput = document.getElementById('apellido_emp');

    // Asigna el evento "input" para convertir a mayúsculas mientras se escribe
    nombreInput.addEventListener('input', convertirMayusculas);
    apellidoInput.addEventListener('input', convertirMayusculas);
</script>
<script>
    document.getElementById('syncButton').addEventListener('click', function () {
        Swal.fire({
            title: 'Sincronizando datos...',
            html: '<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            timerProgressBar: true,
            didOpen: () => {
                // Enviar solicitud para ejecutar el procedimiento almacenado
                fetch('./PHP/sincronizar_datos.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sincronización completada',
                                text: 'Los datos han sido actualizados correctamente.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(data.message || 'Error desconocido.');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message,
                            showConfirmButton: true
                        });
                    });
            }
        });
    });
</script>

</body>
</html>
