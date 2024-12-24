<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ejecutar el procedimiento almacenado sp_ObtenerSaldos
$proc = "{call sp_ObtenerSaldos2()}";
$stmt = sqlsrv_query($conn, $proc);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$saldos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $saldos[] = $row;
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla saldos</title>
    <link rel="icon" href="./IMG/logo.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>


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

        .container {
            max-width: 1738px;
            margin: 0 auto;
            padding: 20px;
        }

        .table th,
        .table td {
            font-size: 1.05em;
            padding: 12px;
        }

        input[type="date"] {
            max-width: 120px;
            /* Ajusta el ancho máximo */
        }

        .d-flex {
            gap: 5px;
            /* Espacio entre los elementos */
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
                display: none;
                /* Oculta el logo en dispositivos móviles */
            }
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
        <h2 class="text-center">Saldos por Fecha</h2>
        <div class="d-flex align-items-center mb-3">
            <label for="startDate" class="mr-2">Desde:</label>
            <input type="date" id="startDate" class="form-control form-control-sm" style="max-width: 120px;">

            <label for="endDate" class="ml-3 mr-2">Hasta:</label>
            <input type="date" id="endDate" class="form-control form-control-sm" style="max-width: 120px;">

            <button id="filterButton" class="btn btn-primary ml-2">Filtrar</button>
            <button id="clearFilterButton" class="btn btn-secondary ml-2">Limpiar Filtros</button>
        </div>

        <!-- Tabla principal con fila de totales incluida -->
        <table id="saldosTable" class="table table-bordered table-responsive" style="width: 100%; font-size: 16px;">
            <thead class="thead-light">
                <tr>
                    <th>Mes</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Finca</th>
                    <th>Desayuno.S</th>
                    <th>Almuerzo.S</th>
                    <th>Almuerzo.P</th>
                    <th>Merienda.S</th>
                    <th>Refrigerio.S</th>
                    <th>Total.S</th>
                    <th>Total.P</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saldos as $row): ?>
                <tr data-date="<?php echo htmlspecialchars($row['fecha']->format('Y-m-d')); ?>">
                    <td><?php echo htmlspecialchars($row['fecha']->format('Y-m-d')); ?></td>
                    <td><?php echo htmlspecialchars($row['cedula_emp']); ?></td>
                    <td><?php echo htmlspecialchars($row['NOMBRE_EMP']); ?></td>
                    <td><?php echo htmlspecialchars($row['APELLIDO_EMP']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_gfc']); ?></td>
                    <td><?php echo htmlspecialchars($row['desayuno_subsidio']); ?></td>
                    <td><?php echo htmlspecialchars($row['almuerzo_subsidio']); ?></td>
                    <td><?php echo htmlspecialchars($row['almuerzo_empresa']); ?></td>
                    <td><?php echo htmlspecialchars($row['merienda_subsidio']); ?></td>
                    <td><?php echo htmlspecialchars($row['refrigerio_subsidio']); ?></td>
                    <td><?php echo number_format($row['total_subsidios'], 2); ?> $</td>
                    <td><?php echo number_format($row['total_empresa'], 2); ?> $</td>
                    <td><?php echo number_format($row['total_subsidios'] + $row['total_empresa'], 2); ?> $</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <!-- Fila de totales dentro de la misma tabla -->
            <tfoot>
                <tr class="totales">
                    <td colspan="5">Totales:</td>
                    <td id="totalDesayunoS">0.00 $</td>
                    <td id="totalAlmuerzoS">0.00 $</td>
                    <td id="totalAlmuerzoP">0.00 $</td>
                    <td id="totalMeriendaS">0.00 $</td>
                    <td id="totalRefrigerioS">0.00 $</td>
                    <td id="totalS">0.00 $</td>
                    <td id="totalP">0.00 $</td>
                    <td id="totalGeneral">0.00 $</td>
                </tr>
            </tfoot>
        </table>
        <button id="exportButton" class="btn btn-success ml-2">Exportar a Excel</button>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function () {
            var table = $('#saldosTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true
            });

            function actualizarTotales() {
                let totalDesayunoS = 0, totalAlmuerzoS = 0, totalAlmuerzoP = 0,
                    totalMeriendaS = 0, totalRefrigerioS = 0, totalS = 0, totalP = 0, totalGeneral = 0;

                table.rows({ filter: 'applied' }).every(function () {
                    let data = this.data();

                    totalDesayunoS += parseFloat(data[4]) || 0;
                    totalAlmuerzoS += parseFloat(data[5]) || 0;
                    totalAlmuerzoP += parseFloat(data[6]) || 0;
                    totalMeriendaS += parseFloat(data[7]) || 0;
                    totalRefrigerioS += parseFloat(data[8]) || 0;
                    totalS += parseFloat(data[9]) || 0;
                    totalP += parseFloat(data[10]) || 0;
                    totalGeneral += parseFloat(data[11]) || 0;
                });

                $('#totalDesayunoS').text(totalDesayunoS.toFixed(2) + " $");
                $('#totalAlmuerzoS').text(totalAlmuerzoS.toFixed(2) + " $");
                $('#totalAlmuerzoP').text(totalAlmuerzoP.toFixed(2) + " $");
                $('#totalMeriendaS').text(totalMeriendaS.toFixed(2) + " $");
                $('#totalRefrigerioS').text(totalRefrigerioS.toFixed(2) + " $");
                $('#totalS').text(totalS.toFixed(2) + " $");
                $('#totalP').text(totalP.toFixed(2) + " $");
                $('#totalGeneral').text(totalGeneral.toFixed(2) + " $");
            }

            actualizarTotales();

            table.on('draw', function () {
                actualizarTotales();
            });

            $('#filterButton').click(function () {
                let startDate = $('#startDate').val();
                let endDate = $('#endDate').val();

                $.fn.dataTable.ext.search.pop();
                $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                    let rowDate = new Date(data[0]);
                    let start = new Date(startDate);
                    let end = new Date(endDate);
                    return (startDate === "" || rowDate >= start) && (endDate === "" || rowDate <= end);
                });

                table.draw();
            });

            $('#clearFilterButton').click(function () {
                $('#startDate').val('');
                $('#endDate').val('');
                $.fn.dataTable.ext.search.pop();
                table.draw();
            });

            $('#exportButton').click(function () {
                var wb = XLSX.utils.book_new();
                var ws = XLSX.utils.json_to_sheet(table.rows({ filter: 'applied' }).data().toArray().map(function (row) {
                    return {
                        "Mes": row[0],
                        "Cédula": row[1],
                        "Nombre": row[2],
                        "Apellido": row[3],
                        "Desayuno.S": row[4],
                        "Almuerzo.S": row[5],
                        "Almuerzo.P": row[6],
                        "Merienda.S": row[7],
                        "Refrigerio.S": row[8],
                        "Total.S": row[9],
                        "Total.P": row[10],
                        "TOTAL": row[11]
                    };
                }));

                XLSX.utils.book_append_sheet(wb, ws, "Saldos");
                XLSX.writeFile(wb, "saldos.xlsx");
            });
        });
    </script>

</body>

</html>