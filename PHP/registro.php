<?php
include 'db.php';
require('fpdf/fpdf.php'); // Incluir la librería FPDF

// Establecer la zona horaria
 date_default_timezone_set('America/Guayaquil');

//Fecha y hora actuales
 $fechaActual = date('Y-m-d');
 $horaActual = date('H:i:s');
//$fechaActual = '2024-11-30';
//$horaActual = '12:30:00';

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

// Obtener el id_com y subsidio de la tabla HorariosComida
$sqlObtenerIdCom = "SELECT id_com, tipo, subS FROM [dbo].[HorariosComida] WHERE tipo = ?";
$paramsObtenerIdCom = array($tipoComida);
$stmtObtenerIdCom = sqlsrv_query($conn, $sqlObtenerIdCom, $paramsObtenerIdCom);
$resultObtenerIdCom = sqlsrv_fetch_array($stmtObtenerIdCom, SQLSRV_FETCH_ASSOC);

if ($resultObtenerIdCom === false || empty($resultObtenerIdCom['id_com'])) {
    header("Location: ../index_asis.php?success=false&error=tipo_comida_no_encontrado&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

$id_com = $resultObtenerIdCom['id_com'];
$subsidioMensual = $resultObtenerIdCom['subS'];

// Verificar si existe un valor programado para el tipo de comida y fecha
$sqlComidaProgramada = "
    SELECT tipoCom, Valor 
    FROM [dbo].[Comidas_ref_for] 
    WHERE id_com = ? 
      AND fechaInicio <= ? 
      AND fechaFinal >= ?
";
$paramsComidaProgramada = array($id_com, $fechaActual, $fechaActual);
$stmtComidaProgramada = sqlsrv_query($conn, $sqlComidaProgramada, $paramsComidaProgramada);

if ($stmtComidaProgramada === false) {
    die(print_r(sqlsrv_errors(), true)); // Manejo de errores
}

$tipoComidaProgramada = null;
$valorComidaProgramada = 0;

if ($rowComidaProgramada = sqlsrv_fetch_array($stmtComidaProgramada, SQLSRV_FETCH_ASSOC)) {
    $tipoComidaProgramada = $rowComidaProgramada['tipoCom']; // Tipo de comida programada
    $valorComidaProgramada = $rowComidaProgramada['Valor']; // Valor programado
}

// Determinar el valor final de la comida y el tipo a mostrar
if ($tipoComidaProgramada) {
    $tipoComida = $tipoComidaProgramada; // Sobrescribir con el tipo de comida programada
    $valorComida = $valorComidaProgramada > 0 ? $valorComidaProgramada : $subsidioMensual;
} else {
    $valorComida = $subsidioMensual; // Usar valor normal si no hay programación
}

// Verificar si ya existe un registro para este tipo de comida
$sqlCheck = "SELECT COUNT(*) AS count FROM [dbo].[registros] WHERE codigo_emp = ? AND fecha = ? AND id_com = ?";
$paramsCheck = array($codigo_emp, $fechaActual, $id_com);
$stmtCheck = sqlsrv_query($conn, $sqlCheck, $paramsCheck);
$resultCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

// Validar el número de registros según el tipo de comida
if ($tipoComida === 'Refrigerio') {
    if ($resultCheck['count'] >= 2) {
        header("Location: ../index_asis.php?success=false&error=refrigerio_maximo&cedula_temp=" . urlencode($cedula_emp));
        exit();
    }
} else {
    if ($resultCheck['count'] > 0) {
        header("Location: ../index_asis.php?success=false&error=registro_existente_en_rango&cedula_temp=" . urlencode($cedula_emp));
        exit();
    }
}

// Insertar el registro en la base de datos con el valor correspondiente
$sqlInsert = "INSERT INTO [dbo].[registros] (codigo_emp, fecha, hora, id_com, valor_registro) VALUES (?, ?, ?, ?, ?)";
$paramsInsert = array($codigo_emp, $fechaActual, $horaActual, $id_com, $valorComida);
$stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

if ($stmtInsert === false) {
    header("Location: ../index_asis.php?success=false&error=guardado_fallido&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

// Obtener el saldo disponible del empleado
$sqlObtenerSaldo = "SELECT saldo FROM [dbo].[Saldo_extras] WHERE codigo_emp = ?";
$paramsObtenerSaldo = array($codigo_emp);
$stmtObtenerSaldo = sqlsrv_query($conn, $sqlObtenerSaldo, $paramsObtenerSaldo);

if ($stmtObtenerSaldo === false) {
    die(print_r(sqlsrv_errors(), true)); // Manejo de errores si la consulta falla
}

// Asignar el saldo o 0 si no existe
$resultObtenerSaldo = sqlsrv_fetch_array($stmtObtenerSaldo, SQLSRV_FETCH_ASSOC);
$saldoDisponible = $resultObtenerSaldo['saldo'] ?? 0;

// Formatear el saldo disponible
$saldoDisponible = number_format($saldoDisponible, 2);

// Generar el PDF del ticket
$pdf = new FPDF('P', 'mm', array(100, 148)); // Formato Postcard 100 x 148 mm
$pdf->AddPage();
$pdf->SetMargins(1, 0, 1); // Márgenes ajustados (1 mm izquierda/derecha, 0 mm superior)
$pdf->SetY(0); // Comienza en la posición 0 para pegarse al borde superior

// Título alineado a la izquierda con líneas decorativas y logo
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, '---------------------------------------------------------------------------------------', 0, 1, 'L'); 
$pdf->Cell(55, 5, '                Ticket de Registro', 0, 0, 'L'); 
$pdf->Image('../IMG/logo.png', 50, $pdf->GetY() - 4, 14, 14);
$pdf->Ln(7); // Salto de línea
$pdf->Cell(0, 5, '---------------------------------------------------------------------------------------', 0, 1, 'L');

// Detalles del ticket
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, 'Fecha y Hora:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, $fechaActual . ' - ' . $horaActual, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, 'Nombre:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, explode(' ', $nombre_emp)[0] . ' ' . explode(' ', $apellido_emp)[0], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, utf8_decode('Cédula:'), 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, $cedula_emp, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, 'Tipo de comida:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, $tipoComida, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(40, 5, 'Valor Consumo:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, '$' . number_format($valorComida, 2), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(30, 5, 'Saldo Extras:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, '$' . number_format($saldoDisponible, 2), 0, 1, 'L');

// Línea decorativa
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, '---------------------------------------------------------------------------------------', 0, 1, 'L');

// Guardar y enviar a imprimir
$pdfDir = 'C:\\pdfcomedor\\'; 
$pdfFile = $pdfDir . 'ticket_' . $cedula_emp . '_' . time() . '.pdf';
$pdf->Output('F', $pdfFile);

// Ruta de SumatraPDF
$sumatraPdfPath = 'C:\\Users\\administrator\\AppData\\Local\\SumatraPDF\\SumatraPDF.exe';
$printerName = "EPSON TM-T20III Receipt";

// Imprimir usando SumatraPDF
$command = '"' . $sumatraPdfPath . '" -print-to "' . $printerName . '" "' . $pdfFile . '"';
exec($command);

// Redirigir al usuario
header("Location: ../index_asis.php?success=true&cedula_temp=" . urlencode($cedula_emp));
exit();
?>
