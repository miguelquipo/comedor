<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

// Conexión a SQL Server
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Obtener datos de HorariosComida
$sql = "SELECT * FROM [dbo].[HorariosComida]";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$horariosComida = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $horariosComida[] = $row;
}

// Obtener el último registro de Comidas_ref_for
$sqlLastRecord = "SELECT * FROM [dbo].[Comidas_ref_for] ORDER BY id_reffor DESC";
$stmtLastRecord = sqlsrv_query($conn, $sqlLastRecord);

$tiposComida = [];
if ($stmtLastRecord !== false) {
    while ($row = sqlsrv_fetch_array($stmtLastRecord, SQLSRV_FETCH_ASSOC)) {
        $tiposComida[] = $row;
    }
}

// Manejar la eliminación de registros
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_to_delete = $_POST['eliminar_id'];
    $sqlDelete = "DELETE FROM [dbo].[Comidas_ref_for] WHERE id_reffor = ?";
    $params = [$id_to_delete];
    $stmtDelete = sqlsrv_query($conn, $sqlDelete, $params);

    if ($stmtDelete === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=delete_success");
    exit;
}

// Cerrar la conexión
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Subsidios</title>
    <link rel="icon" href="./IMG/logo.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .icon-delete {
            cursor: pointer;
            color: red;
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
        .card {
    border-radius: 15px; /* Bordes redondeados */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.7); /* Sombra más fuerte */
}

    /* Eliminar los bordes visibles en el encabezado y cuerpo de la tarjeta */
    .card-header, .card-body {
        border: none; /* Eliminar líneas del borde */
    }

    /* Personalización de las tablas dentro de las tarjetas */
    .table th, .table td {
        border: 1px solid #dee2e6; /* Mantener líneas en las tablas */
    }

    /* Asegurarse de que el botón también tenga bordes redondeados */
    .btn {
        border-radius: 25px; /* Bordes redondeados en botones */
    }

    /* Margen entre las tarjetas */
    .card + .card {
        margin-top: 30px;
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

</style>

</head>
<body>
<img src="./IMG/logo.png" alt="Logo de la empresa" class="logo">
<button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <button class="btn-return btn btn-primary" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <div class="container mt-5">
    <!-- Card para configurar subsidios -->
    <div class="card mb-4 rounded-3 shadow-sm border-0">
        <div class="card-header bg-transparent border-0">
            <h2>Configurar Subsidios</h2>
        </div>
        <div class="card-body">
            <form action="./PHP/guardar_subsidios.php" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Subsidio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horariosComida as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                            <td>
                                <input type="number" name="subsidios[<?php echo htmlspecialchars($row['tipo']); ?>]"
                                       value="<?php echo htmlspecialchars($row['subS']); ?>" class="form-control" step="0.01">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <div class="container mt-5">
    <!-- Card para la programación de comida reforzada/forzada -->
    <div class="card mb-4 rounded-3 shadow-sm border-0">
        <div class="card-header bg-transparent border-0">
            <h2>Programación Comida Reforzada / Forzada</h2>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo de Comida</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Valor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tiposComida as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['tipoCom']); ?></td>
                        <td><?php echo isset($registro['fechaInicio']) ? $registro['fechaInicio']->format('Y-m-d') : ''; ?></td>
                        <td><?php echo isset($registro['fechaFinal']) ? $registro['fechaFinal']->format('Y-m-d') : ''; ?></td>
                        <td><?php echo htmlspecialchars($registro['Valor']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="eliminar_id" value="<?php echo $registro['id_reffor']; ?>">
                                <button type="submit" class="btn btn-link p-0" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                                    <i class="fas fa-trash icon-delete"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <form id="comidaForm" method="POST" action="./PHP/guardar_comida_ref_for.php">
                            <td>
                                <!-- Contenedor para el select y el input editable -->
                                <div class="input-group">
                                    <!-- Select para elegir el tipo de comida -->
                                    <select id="tipo_comida_select" class="form-control" style="max-width: 150px;">
                                        <option value="Desayuno">Desayuno</option>
                                        <option value="Almuerzo">Almuerzo</option>
                                        <option value="Merienda">Merienda</option>
                                        <option value="Refrigerio">Refrigerio</option>
                                    </select>
                                    <!-- Input de texto para la especificación -->
                                    <input type="text" id="tipo_comida_input" class="form-control" placeholder="Especificación (ej. completo)">
                                </div>
                                <!-- Campo oculto que contendrá la combinación y será enviado en el formulario -->
                                <input type="hidden" name="tipoComida" id="tipo_comida" required>
                            </td>
                            <td>
                                <input type="date" name="fechaInicio" id="fecha_inicio" class="form-control" required>
                            </td>
                            <td>
                                <input type="date" name="fechaFinal" id="fecha_final" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="valor" id="valor" class="form-control" min="0.01" step="0.01" required placeholder="Valor">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary">Agregar</button>
                            </td>
                        </form>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Actualiza el valor del campo oculto `tipoComida` cuando cambie el select o el input de especificación
    function updateTipoComida() {
        const tipoPrincipal = document.getElementById('tipo_comida_select').value;
        const especificacion = document.getElementById('tipo_comida_input').value.trim();
        document.getElementById('tipo_comida').value = `${tipoPrincipal} ${especificacion}`.trim();
    }

    // Escucha cambios en el select y el input de especificación
    document.getElementById('tipo_comida_select').addEventListener('change', updateTipoComida);
    document.getElementById('tipo_comida_input').addEventListener('input', updateTipoComida);

    // Validaciones al enviar el formulario
    document.getElementById('comidaForm').addEventListener('submit', function(event) {
        updateTipoComida(); // Asegura que el valor esté actualizado al enviar

        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFinal = document.getElementById('fecha_final').value;
        const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha de hoy en formato YYYY-MM-DD

        // Validación de fechas
        if (fechaInicio <= today) {
            event.preventDefault();
            Swal.fire({
                title: 'Error',
                text: 'La fecha de inicio debe ser una fecha futura, no el día actual ni una fecha pasada.',
                icon: 'error'
            });
            return;
        }

        if (fechaFinal < fechaInicio) {
            event.preventDefault();
            Swal.fire({
                title: 'Error',
                text: 'La fecha final debe ser igual o posterior a la fecha de inicio.',
                icon: 'error'
            });
            return;
        }
    });

    // SweetAlert para mostrar mensajes según los parámetros en la URL
    if (window.location.search.includes("msg")) {
        const params = new URLSearchParams(window.location.search);
        const msg = params.get("msg");
        let config = {};

        switch (msg) {
            case 'delete_success':
                config = { title: '¡Éxito!', text: 'El registro fue eliminado correctamente.', icon: 'success' };
                break;
            case 'duplicate_records':
                config = { title: 'Advertencia', text: 'Ya hay dos o más programaciones con la misma fecha y valor.', icon: 'warning' };
                break;
            case 'ongoing_program':
                config = { title: 'Advertencia', text: 'Hay una programación en curso del mismo tipo de comida.', icon: 'warning' };
                break;
            case 'success':
                config = { title: '¡Éxito!', text: 'El nuevo tipo de comida fue agregado correctamente.', icon: 'success' };
                break;
            default:
                config = { title: 'Error', text: 'Ocurrió un error.', icon: 'error' };
        }

        Swal.fire(config).then(function() {
            window.location = 'configurarS.php';  // Redirige a la página configurarS.php
        });
    }
</script>

        <?php if (isset($_GET['success_S'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Los cambios se guardaron correctamente.',
            showConfirmButton: true
        }).then(function() {
            window.location = 'configurarS.php';  // Redirige a la página configurarS.php
        });
    </script>
<?php elseif (isset($_GET['error_S'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'Hubo un problema al guardar los cambios.',
            showConfirmButton: true
        }).then(function() {
            window.location = 'configurarS.php';  // Redirige a la página configurarS.php
        });
    </script>
<?php endif; ?>

  
</body>
</html>
