<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Consulta para obtener los datos de la tabla
$query = "SELECT NOMBRE_EMP, APELLIDO_EMP, CEDULA_EMP, NOMBRE_EFC, NOMBRE_GFC FROM personal WHERE NOMBRE_EST = 'Activo'";
$stmt = sqlsrv_query($conn, $query);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rows = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $rows[] = $row;
}

// Consulta para obtener los valores únicos de NOMBRE_GFC
$queryDistinct = "SELECT DISTINCT NOMBRE_GFC FROM personal WHERE NOMBRE_EST = 'Activo'";
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
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>

<body>
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
        <table id="personalTable" class="table table-bordered table-striped" style="width: 100%;">
            <thead class="thead-light">
                <tr>
                    <th>Seleccionar</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
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
                        <?php echo htmlspecialchars($row['NOMBRE_EMP']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['APELLIDO_EMP']); ?>
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
        $('#generateTickets').on('click', function () {
            // Obtener las cédulas seleccionadas
            var selectedCedulas = [];
            $('.select-row:checked').each(function () {
                selectedCedulas.push($(this).val());
            });

            if (selectedCedulas.length === 0) {
                alert('Por favor selecciona al menos un registro.');
                return;
            }

            // Enviar las cédulas al servidor
            $.ajax({
                url: './PHP/procesar_tickets.php', // Archivo PHP que manejará la generación
                method: 'POST',
                data: { cedulas: selectedCedulas },
                success: function (response) {
                    alert(response.message);
                },
                error: function () {
                    alert('Ocurrió un error al generar los tickets.');
                }
            });
        });

    </script>
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

            // Obtener filas seleccionadas
            $('#getSelected').click(function () {
                var selected = [];
                $('.select-row:checked').each(function () {
                    selected.push($(this).val());
                });
                alert('Cédulas seleccionadas: ' + selected.join(', '));
            });
        });
    </script>
</body>

</html>