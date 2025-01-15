<?php
include './PHP/Usuarios/check_access.php';

// Asegura que solo los usuarios con role_id 3 (editor) o 1 (admin) puedan acceder
checkAccess([1, 3]);
?>
<?php

// Incluir el archivo de configuración actual
$configFile = './PHP/config_horarios.php';
$horarios = include $configFile;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevosHorarios = [];
    foreach ($_POST['tipo'] as $key => $tipo) {
        $inicio = $_POST['inicio'][$key];
        $fin = $_POST['fin'][$key];
        $nuevosHorarios[] = ['tipo' => $tipo, 'inicio' => $inicio, 'fin' => $fin];
    }

    $contenido = "<?php\nreturn " . var_export($nuevosHorarios, true) . ";\n";
    file_put_contents($configFile, $contenido);

    header('Location: editar_horarios.php?success=true');
    exit();
}

$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horarios de Comida</title>
    <link rel="icon" href="./IMG/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #e9ecef;
        }
        .logo {
  position: absolute;
  top: 1px;
  right: 70px;
  width: 130px;
  height: auto;
}

.logo-fixed {
  position: fixed;
  top: 10px;
  right: 10px;
  transition: top 0.3s ease;
}
/* Cambios para dispositivos móviles */
@media only screen and (max-width: 900px) {
  .logo {
    display: none; /* Oculta el logo en dispositivos móviles */
  }
}
.logout-button {
            position: fixed;
            bottom: 70px;
            /* Ajusta la distancia desde la parte inferior */
            right: 20px;
            /* Ajusta la distancia desde la parte derecha */
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999;
            /* Asegúrate de que el botón esté sobre otros elementos */
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
</style>

</head>
<body>
<img src="./IMG/logo.png" alt="Logo de la empresa" class="logo">
<button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <button class="btn-return btn btn-primary" onclick="window.location.href='configurarS.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <div class="container">
        <h1 class="mb-4 text-center">Editar Horarios de Comida</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ¡Horarios actualizados correctamente!
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Comida</th>
                            <th>Hora de Inicio (H:i:s)</th>
                            <th>Hora de Fin (H:i:s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $index => $rango): ?>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" name="tipo[]" value="<?= htmlspecialchars($rango['tipo']) ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="inicio[]" 
                                           value="<?= htmlspecialchars($rango['inicio']) ?>" 
                                           pattern="^(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$" 
                                           required 
                                           placeholder="HH:mm:ss">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="fin[]" 
                                           value="<?= htmlspecialchars($rango['fin']) ?>" 
                                           pattern="^(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$" 
                                           required 
                                           placeholder="HH:mm:ss" 
                                           oninput="ajustarHoraSiguiente(<?= $index ?>)">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para ajustar la hora de inicio de la siguiente comida
        function ajustarHoraSiguiente(index) {
            const inputsFin = document.querySelectorAll('input[name="fin[]"]');
            const inputsInicio = document.querySelectorAll('input[name="inicio[]"]');
            
            if (index < inputsFin.length - 1) {
                const finActual = inputsFin[index].value;
                const siguienteInicio = inputsInicio[index + 1];
                
                // Convertir la hora de fin actual a segundos
                const [horaFin, minutoFin, segundoFin] = finActual.split(':').map(Number);
                const finEnSegundos = horaFin * 3600 + minutoFin * 60 + segundoFin;

                // Añadir 1 segundo al final
                const siguienteHoraInicio = new Date(0);
                siguienteHoraInicio.setSeconds(finEnSegundos + 1);

                // Formatear la nueva hora en HH:mm:ss
                const nuevaHoraInicio = siguienteHoraInicio.toISOString().substr(11, 8);
                
                // Asegurarse de que la nueva hora de inicio sigue el formato correcto
                siguienteInicio.value = nuevaHoraInicio;
                
                // Verificar si la nueva hora de inicio es válida en el formato correcto (HH:mm:ss)
                siguienteInicio.setCustomValidity('');
            }
        }
    </script>
</body>
</html>
