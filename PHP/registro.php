<?php
include 'db.php';
require('fpdf/fpdf.php'); // Incluir la librería FPDF

// Establecer la zona horaria
//date_default_timezone_set('America/Guayaquil');

// Establecer una fecha y hora específica para pruebas
$fechaActual = '2024-11-11'; // Cambia a la fecha que desee
$horaActual = '15:30:00';    // Cambia a la hora que desee

// Si desearas utilizar la fecha y hora actuales, simplemente descomentas las siguientes líneas:
//$fechaActual = date('Y-m-d');
//$horaActual = date('H:i:s');

// Obtener los datos del formulario
$cedula_emp = $_POST['cedula_emp'] ?? null;

if (!$cedula_emp) {
    header("Location: ../index_asis.php?success=false&error=cedula_vacia");
    exit();
}

// Buscar el código y los datos del empleado basado en la cédula
$sqlBuscarEmpleado = "SELECT codigo_emp, NOMBRE_EMP, APELLIDO_EMP FROM [dbo].[personal] WHERE cedula_emp = ?";
$paramsBuscarEmpleado = array($cedula_emp);
$stmtBuscarEmpleado = sqlsrv_query($conn, $sqlBuscarEmpleado, $paramsBuscarEmpleado);
$resultBuscarEmpleado = sqlsrv_fetch_array($stmtBuscarEmpleado, SQLSRV_FETCH_ASSOC);

if ($resultBuscarEmpleado === false || empty($resultBuscarEmpleado['codigo_emp'])) {
    header("Location: ../index_asis.php?success=false&error=cedula_no_encontrada&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

$codigo_emp = $resultBuscarEmpleado['codigo_emp'];
$nombre_emp = $resultBuscarEmpleado['NOMBRE_EMP'];
$apellido_emp = $resultBuscarEmpleado['APELLIDO_EMP'];

// Determinar el tipo de comida basado en la hora actual
if ($horaActual >= '04:00:00' && $horaActual <= '08:00:00') {
    $tipoComida = 'Desayuno';
} elseif ($horaActual >= '08:00:01' && $horaActual <= '11:19:59') {
    $tipoComida = 'Refrigerio';
} elseif ($horaActual >= '11:20:00' && $horaActual <= '15:00:00') {
    $tipoComida = 'Almuerzo';
} elseif ($horaActual >= '15:00:01' && $horaActual <= '17:59:59') {
    $tipoComida = 'Refrigerio';
} elseif ($horaActual > '18:00:00') {
    $tipoComida = 'Merienda';
} else {
    header("Location: ../index_asis.php?success=false&error=hora_fuera_de_rango&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

// Obtener el id_com y subsidio
$sqlObtenerIdCom = "SELECT id_com, subS FROM [dbo].[HorariosComida] WHERE tipo = ?";
$paramsObtenerIdCom = array($tipoComida);
$stmtObtenerIdCom = sqlsrv_query($conn, $sqlObtenerIdCom, $paramsObtenerIdCom);
$resultObtenerIdCom = sqlsrv_fetch_array($stmtObtenerIdCom, SQLSRV_FETCH_ASSOC);

if ($resultObtenerIdCom === false || empty($resultObtenerIdCom['id_com'])) {
    header("Location: ../index_asis.php?success=false&error=tipo_comida_no_encontrado&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

$id_com = $resultObtenerIdCom['id_com'];
$subsidioMensual = $resultObtenerIdCom['subS'];

// Verificar el número de registros previos para el empleado en el día y tipo de comida
$sqlCheck = "SELECT COUNT(*) AS count FROM [dbo].[registros] WHERE codigo_emp = ? AND fecha = ? AND id_com = ?";
$paramsCheck = array($codigo_emp, $fechaActual, $id_com);
$stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
$resultCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

// Validar el número de registros según el tipo de comida
if ($tipoComida === 'Refrigerio') {
    // Permitir hasta 2 registros de refrigerio
    if ($resultCheck['count'] >= 2) {
        header("Location: ../index_asis.php?success=false&error=refrigerio_maximo&cedula_temp=" . urlencode($cedula_emp));
        exit();
    }
} else {
    // Para otros tipos de comida, solo permitir un registro por día
    if ($resultCheck['count'] > 0) {
        header("Location: ../index_asis.php?success=false&error=registro_existente_en_rango&cedula_temp=" . urlencode($cedula_emp));
        exit();
    }
}

// Insertar el registro en la base de datos
$sqlInsert = "INSERT INTO [dbo].[registros] (codigo_emp, fecha, hora, id_com, valor_registro) VALUES (?, ?, ?, ?, ?)";
$paramsInsert = array($codigo_emp, $fechaActual, $horaActual, $id_com, 0); // El valor de "valor_registro" se asignará después
$stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

if ($stmtInsert === false) {
    header("Location: ../index_asis.php?success=false&error=guardado_fallido&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

// Ejecutar el procedimiento almacenado y obtener el total_empresa
$sqlProc = "EXEC [dbo].[sp_ObtenerSaldosPorCedula] ?";
$paramsProc = array($cedula_emp);
$stmtProc = sqlsrv_query($conn, $sqlProc, $paramsProc);

if ($stmtProc === false) {
    die(print_r(sqlsrv_errors(), true)); // Imprime los errores y detiene la ejecución
}

$resultProc = sqlsrv_fetch_array($stmtProc, SQLSRV_FETCH_ASSOC);

if ($resultProc === false || !isset($resultProc['total_empresa'])) {
    header("Location: ../index_asis.php?success=false&error=no_total_empresa&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

$valorDiario = $resultProc['total_empresa']; // Asignar el valor de total_empresa como valor diario

// Actualizar el valor de "valor_registro" con el valor calculado
$sqlUpdate = "UPDATE [dbo].[registros] SET valor_registro = ? WHERE codigo_emp = ? AND fecha = ? AND id_com = ?";
$paramsUpdate = array($valorDiario, $codigo_emp, $fechaActual, $id_com);
$stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $paramsUpdate);

// Obtener el saldo disponible del empleado
$sqlObtenerSaldo = "SELECT saldo FROM [dbo].[Saldo_extras] WHERE codigo_emp = ?";
$paramsObtenerSaldo = array($codigo_emp);
$stmtObtenerSaldo = sqlsrv_query($conn, $sqlObtenerSaldo, $paramsObtenerSaldo);
$resultObtenerSaldo = sqlsrv_fetch_array($stmtObtenerSaldo, SQLSRV_FETCH_ASSOC);

$saldoDisponible = $resultObtenerSaldo['saldo'] ?? 0;

// Generar el PDF del ticket
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, '                                              Ticket de Registro', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, '                             Fecha: ' . $fechaActual, 0, 1);
$pdf->Cell(40, 10, '                             Hora: ' . $horaActual, 0, 1);
$pdf->Cell(40, 10, '                             Empleado: ' . $nombre_emp . ' ' . $apellido_emp, 0, 1);
$pdf->Cell(40, 10, '                             Comida: ' . $tipoComida, 0, 1);
$pdf->Cell(40, 10, '                             Acumulado de consumo: $' . number_format($valorDiario, 2), 0, 1);
$pdf->Cell(40, 10, '                             Saldo Extras: $' . number_format($saldoDisponible, 2), 0, 1);

// Define la ruta completa para guardar el PDF
$pdfDir = 'C:\\pdfcomedor\\'; // Usa doble barra invertida
$pdfFile = $pdfDir . 'ticket_' . $cedula_emp . '_' . time() . '.pdf';
$pdf->Output('F', $pdfFile);

// Cerrar la conexión
sqlsrv_close($conn);

// Redirigir al usuario con el PDF generado y el número de cédula
header("Location: ../index_asis.php?success=true&cedula_temp=" . urlencode($cedula_emp));
exit();
?>
