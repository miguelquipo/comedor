<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['update_table'])) {
    $proc = "{call asistencia_dia_actual()}";
    $stmt = sqlsrv_query($conn, $proc);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $saldos = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $saldos[] = $row;
    }

    sqlsrv_close($conn);

    echo json_encode($saldos);
    exit;
}

$saldos = [];
$proc = "{call asistencia_dia_actual()}";
$stmt = sqlsrv_query($conn, $proc);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $saldos[] = $row;
}

sqlsrv_close($conn);

$saldosJson = json_encode($saldos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Asistencia</title>
    <link rel="icon" href="./IMG/logo.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .config-button {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
        }

        .config-button:hover {
            background-color: #0056b3;
        }


        .table th,
        .table td {
            font-size: 1.05em;
            padding: 12px;
        }

        input[type="date"] {
            max-width: 120px;
        }

        .d-flex {
            gap: 5px;
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

        @media only screen and (max-width: 900px) {
            .logo {
                display: none;
            }
        }
        .container {
            max-width: 1610px;
            margin: 0 auto;
            padding: 20px;
        }
        .table-responsive {
            max-height: 650px;
            overflow-y: auto;
        }
        tfoot {
        position: sticky;
        bottom: 0;
        background-color: #f1f1f1;
        z-index: 1;
        font-weight: bold;
    }

    </style>



</head>
<body>
<img src="./IMG/logo.png" alt="Logo de la empresa" class="logo">
    <button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <button class="btn-return btn btn-primary" onclick="window.location.href='list_reports.php'">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="container mt-5">
        <h2 class="text-center">Datos Día Actual</h2>

        <table id="saldosTable" class="table table-bordered table-responsive" style="width: 100%; font-size: 16px;">
            <thead class="thead-light">
                <tr>
                    <th>Cédula</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Finca</th>
                    <th>Área</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Desayunos</th>
                    <th>Almuerzos</th>
                    <th>Meriendas</th>
                    <th>Refrigerios</th>
                    <th>Total Comidas</th>
                    <th>Total Saldo Ex</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saldos as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['cedula_emp']) ?></td>
                        <td><?= htmlspecialchars($row['APELLIDO_EMP']) ?></td>
                        <td><?= htmlspecialchars($row['NOMBRE_EMP']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_gfc']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_efc']) ?></td>
                        <td><?= htmlspecialchars($row['Fecha']) ?></td>
                        <td><?= isset($row['ultima_hora']) ? htmlspecialchars($row['ultima_hora']) : ''; ?></td>
                        <td><?= htmlspecialchars($row['desayunos']) ?></td>
                        <td><?= htmlspecialchars($row['almuerzos']) ?></td>
                        <td><?= htmlspecialchars($row['meriendas']) ?></td>
                        <td><?= htmlspecialchars($row['refrigerios']) ?></td>
                        <td><?= number_format($row['total_subsidios'], 2) ?> $</td>
                        <td><?= number_format($row['saldo_total'], 2) ?> $</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">Totales:</td>
                    <td id="totalDesayunos"></td>
                    <td id="totalAlmuerzos"></td>
                    <td id="totalMeriendas"></td>
                    <td id="totalRefrigerios"></td>
                    <td id="totalComidas"></td>
                    <td id="totalSaldo"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
      $(document).ready(function () {
    // Inicializar DataTable con pie de página
    var table = $('#saldosTable').DataTable({
        paging: true,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        footerCallback: function (row, data, start, end, display) {
            // Calcular y mostrar totales en el pie de página para los datos visibles
            var api = this.api();

            // Helper para sumar una columna específica
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ? i : 0;
            };

            // Actualizar cada total en el pie de tabla
            $('#totalDesayunos').html(
                api.column(7, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0)
            );
            $('#totalAlmuerzos').html(
                api.column(8, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0)
            );
            $('#totalMeriendas').html(
                api.column(9, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0)
            );
            $('#totalRefrigerios').html(
                api.column(10, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0)
            );
            $('#totalComidas').html(
                api.column(11, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0).toFixed(2) + ' $'
            );
            $('#totalSaldo').html(
                api.column(12, { page: 'current' }).data().reduce((a, b) => intVal(a) + intVal(b), 0).toFixed(2) + ' $'
            );
        }
    });

    // Actualizar los datos cada segundo
    setInterval(function () {
        $.ajax({
            url: '', // Llama a este archivo PHP
            method: 'GET',
            data: { update_table: true },
            dataType: 'json',
            success: function (data) {
                table.clear(); // Limpiar la tabla

                // Agregar filas nuevas
                data.forEach(function (row) {
                    table.row.add([
                        row.cedula_emp || '',
                        row.APELLIDO_EMP || '',
                        row.NOMBRE_EMP || '',
                        row.nombre_gfc || '',
                        row.nombre_efc || '',
                        row.Fecha || '',
                        row.ultima_hora || '',
                        row.desayunos || 0,
                        row.almuerzos || 0,
                        row.meriendas || 0,
                        row.refrigerios || 0,
                        (row.total_subsidios || 0).toFixed(2) + ' $',
                        (row.saldo_total || 0).toFixed(2) + ' $'
                    ]);
                });

                table.draw(); // Redibujar la tabla y actualizar el pie de página
            }
        });
    }, 1000); // Intervalo de 1 segundo
});

    </script>

</body>
</html>
