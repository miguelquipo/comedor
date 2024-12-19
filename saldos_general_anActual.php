<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1, 3]);
include './PHP/db.php';

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ejecutar el procedimiento almacenado sp_ObtenerSaldos
$proc = "{call saldos_general_anActual()}";
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
            max-width: 1164px;
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
    <button class="btn-return btn btn-primary" onclick="window.location.href='list_reports.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    
    <div class="container mt-5">
        <h2 class="text-center">Datos Mes Actual</h2>
        
        <table id="saldosTable" class="table table-bordered table-responsive" style="width: 100%; font-size: 16px;">
            <thead class="thead-light">
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Desayunos</th>
                    <th>Almuerzos</th>
                    <th>Meriendas</th>
                    <th>Refrigerios</th>
                    <th>Total Comidas</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($saldos as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cedula_emp']); ?></td>
                        <td><?php echo htmlspecialchars($row['NOMBRE_EMP']); ?></td>
                        <td><?php echo htmlspecialchars($row['APELLIDO_EMP']); ?></td>
                        <td><?php echo htmlspecialchars($row['desayunos']); ?></td>
                        <td><?php echo htmlspecialchars($row['almuerzos']); ?></td>
                        <td><?php echo htmlspecialchars($row['meriendas']); ?></td>
                        <td><?php echo htmlspecialchars($row['refrigerios']); ?></td>
                        <td><?php echo number_format($row['total_consumo'], 2); ?> $</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <!-- Fila de totales -->
            <tfoot>
                <tr>
                    <td colspan="3">Totales:</td>
                    <td id="totalDesayunos">0</td>
                    <td id="totalAlmuerzos">0</td>
                    <td id="totalMeriendas">0</td>
                    <td id="totalRefrigerios">0</td>
                    <td id="totalComidas">0.00 $</td>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success ml-2" onclick="exportTableToExcel('saldosTable', 'Saldos_fecha')">
                Exportar a Excel
            </button>
        </div>
    </div>

    <style>
        table {
            table-layout: auto;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            font-size: 18px;
        }

        td {
            font-size: 16px;
        }
    </style>

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

        // Función para actualizar los totales
        function updateTotals() {
            var totalDesayunos = 0, totalAlmuerzos = 0, totalMeriendas = 0, totalRefrigerios = 0, totalComidas = 0;

            // Iterar sobre las filas visibles de la tabla
            table.rows({ search: 'applied' }).every(function() {
                var data = this.data();
                totalDesayunos += parseInt(data[3]) || 0; // Desayunos
                totalAlmuerzos += parseInt(data[4]) || 0; // Almuerzos
                totalMeriendas += parseInt(data[5]) || 0; // Meriendas
                totalRefrigerios += parseInt(data[6]) || 0; // Refrigerios
                totalComidas += parseFloat(data[7].replace('$', '').trim()) || 0; // Total Comidas
            });

            // Actualizar los totales en el pie de la tabla
            $('#totalDesayunos').text(totalDesayunos);
            $('#totalAlmuerzos').text(totalAlmuerzos);
            $('#totalMeriendas').text(totalMeriendas);
            $('#totalRefrigerios').text(totalRefrigerios);
            $('#totalComidas').text(totalComidas.toFixed(2) + ' $');
        }

        // Llamar a updateTotals cada vez que se redibuje la tabla (filtro, paginación, ordenación)
        table.on('draw', function () {
            updateTotals();
        });

        // Llamar a updateTotals al cargar la página
        updateTotals();
    });

    function exportTableToExcel(tableID, filename = '') {
        var table = $('#saldosTable').DataTable();

        // Desactiva paginación y recarga todos los datos en el DOM.
        table.page.len(-1).draw();

        var tableSelect = document.getElementById(tableID);

        // Añadir apóstrofe para evitar errores de fechas en Excel
        [...tableSelect.querySelectorAll('td:first-child')].forEach(cell => {
            cell.innerHTML = `'${cell.innerHTML}`; 
        });

        var wb = XLSX.utils.table_to_book(tableSelect, { sheet: "Resultados" });
        XLSX.writeFile(wb, filename ? filename + '.xlsx' : 'ExcelData.xlsx');
    }
</script>

</body>

</html>
