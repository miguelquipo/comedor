<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Consulta para obtener los datos de la tabla
$query = "SELECT NOMBRE_EMP, APELLIDO_EMP, CEDULA_EMP, NOMBRE_EFC, NOMBRE_GFC FROM personal WHERE NOMBRE_EST = 'Activo'  ";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rows = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $rows[] = $row;
}

// Consulta para obtener los valores únicos de NOMBRE_GFC
$queryDistinct = "SELECT DISTINCT NOMBRE_GFC FROM personal WHERE NOMBRE_EST = 'Activo' and NOMBRE_GFC is not null";
$stmtDistinct = sqlsrv_query($conn, $queryDistinct);
if ($stmtDistinct === false) {
    die(print_r(sqlsrv_errors(), true));
}

$uniqueGFCs = [];
while ($row = sqlsrv_fetch_array($stmtDistinct, SQLSRV_FETCH_ASSOC)) {
    $uniqueGFCs[] = $row['NOMBRE_GFC'];
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Personal Activo</title>
    <link rel="icon" href="./IMG/logo.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Asegúrate de tener SweetAlert2 -->
    <style>
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
            bottom: 70px;
            right: 20px;
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999;
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
<button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <button class="btn-return btn btn-primary" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <div class="container mt-5">
        <h2 class="text-center">Personal Activo</h2>
        <div class="form-group">
    <label for="filterGFC">Filtrar por NOMBRE_GFC:</label>
    <select id="filterGFC" class="form-control" style="max-width: 300px;">
        <option value="">Todos</option>
        <?php foreach ($uniqueGFCs as $gfc): ?>
        <option value="<?php echo htmlspecialchars($gfc); ?>">
            <?php echo htmlspecialchars($gfc); ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Botón para seleccionar/deseleccionar todos -->
<div class="form-group">
    <button id="selectAll" class="btn btn-secondary">Seleccionar Todo</button>
</div>

<table id="personalTable" class="table table-bordered table-striped" style="width: 100%;">
    <thead class="thead-light">
        <tr>
            <th>Seleccionar</th>
            <th>Cédula</th>
            <th>Apellido</th>
            <th>Nombre</th>
            <th>Entidad</th>
            <th>Grupo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr data-gfc="<?php echo htmlspecialchars($row['NOMBRE_GFC']); ?>">
            <td>
                <input type="checkbox" class="select-row"
                    value="<?php echo htmlspecialchars($row['CEDULA_EMP']); ?>">
            </td>
            <td>
                <?php echo htmlspecialchars($row['CEDULA_EMP']); ?>
            </td>
            <td>
                <?php echo htmlspecialchars($row['APELLIDO_EMP']); ?>
            </td>
            <td>
                <?php echo htmlspecialchars($row['NOMBRE_EMP']); ?>
            </td>
            <td>
                <?php echo htmlspecialchars($row['NOMBRE_EFC']); ?>
            </td>
            <td>
                <?php echo htmlspecialchars($row['NOMBRE_GFC']); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

        <button id="generateTickets" class="btn btn-primary">Generar Tickets</button>
    </div>
    <script>
        $(document).ready(function () {
    // Inicializar DataTable
    var table = $('#personalTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true
    });

    // Filtrar tabla al seleccionar una opción en el select
    $('#filterGFC').change(function () {
        var selectedGFC = $(this).val();
        if (selectedGFC) {
            table.columns(5).search('^' + selectedGFC + '$', true, false).draw();
        } else {
            table.columns(5).search('').draw();
        }
    });

    // Botón para seleccionar/deseleccionar todas las filas visibles
    let allSelected = false;
    $('#selectAll').click(function () {
        allSelected = !allSelected;
        $('.select-row').prop('checked', false); // Reiniciar selección antes de aplicar nueva
        var visibleRows = table.rows({ search: 'applied' }).nodes();

        if (allSelected) {
            $(visibleRows).find('.select-row').prop('checked', true);
            $(this).text('Deseleccionar Todo'); // Cambiar texto del botón
        } else {
            $(visibleRows).find('.select-row').prop('checked', false);
            $(this).text('Seleccionar Todo'); // Cambiar texto del botón
        }
    });
});

    </script>
    <script>
        $('#generateTickets').on('click', function () {
            // Obtener las cédulas seleccionadas
            var selectedCedulas = [];
            $('.select-row:checked').each(function () {
                selectedCedulas.push($(this).val());
            });

            if (selectedCedulas.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona al menos un registro',
                    text: 'Por favor selecciona al menos un registro para generar los tickets.',
                    showConfirmButton: true
                });
                return;
            }

            // Enviar las cédulas al servidor
            $.ajax({
                url: './PHP/procesar_tickets.php', // Archivo PHP que manejará la generación
                method: 'POST',
                data: { cedulas: selectedCedulas },
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tickets Generados',
                            text: res.message,
                            showConfirmButton: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al generar tickets',
                            text: res.message,
                            showConfirmButton: true
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar los tickets.',
                        showConfirmButton: true
                    });
                }
            });
        });

  
    </script>
</body>

</html>
