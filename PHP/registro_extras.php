<?php
// Incluir archivo de conexión
include('db.php');
date_default_timezone_set('America/Chicago');

// Extraer y limpiar los datos del formulario
$cedula_emp = isset($_POST['cedula_emp']) ? trim($_POST['cedula_emp']) : '';
$valor = isset($_POST['valor']) ? (float)$_POST['valor'] : 0;
$producto = isset($_POST['producto']) ? trim($_POST['producto']) : '';

// Verificar si la cédula no está vacía
if (empty($cedula_emp)) {
    header("Location: ../index_extras.php?success=false&error=cedula_no_encontrada");
    exit();
}

// Validar conexión a la base de datos
if ($conn === false) {
    die("Error en la conexión: " . print_r(sqlsrv_errors(), true));
}

// Buscar el codigo_emp y los nombres desde la tabla personal usando la cédula correcta
$sql_codigo_emp = "SELECT codigo_emp, nombre_emp, apellido_emp FROM personal WHERE LTRIM(RTRIM(CEDULA_EMP)) = ?";
$params_codigo_emp = array($cedula_emp);
$stmt_codigo_emp = sqlsrv_query($conn, $sql_codigo_emp, $params_codigo_emp);

if ($stmt_codigo_emp === false) {
    die("Error en la consulta de código de empleado: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt_codigo_emp, SQLSRV_FETCH_ASSOC);
if (!$row) {
    header("Location: ../index_extras.php?success=false&error=cedula_no_encontrada");
    exit();
}

$codigo_emp = $row['codigo_emp'];
$nombre_emp = $row['nombre'];
$apellido_emp = $row['apellido'];

// Verificar si existe saldo para el empleado
$sql_saldo = "SELECT saldo FROM Saldo_extras WHERE codigo_emp = ?";
$params_saldo = array($codigo_emp);
$stmt_saldo = sqlsrv_query($conn, $sql_saldo, $params_saldo);
$row_saldo = sqlsrv_fetch_array($stmt_saldo, SQLSRV_FETCH_ASSOC);

if ($row_saldo === null) {
    header("Location: ../index_extras.php?success=false&error=saldo_no_encontrado&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

$saldo_actual = $row_saldo['saldo'];
$nuevo_saldo = $saldo_actual - $valor;

// Validar el nuevo saldo
if ($nuevo_saldo < -2) {
    header("Location: ../index_extras.php?success=false&error=saldo_insuficiente&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

if ($nuevo_saldo < 0 && $nuevo_saldo >= -2) {
    header("Location: ../index_extras.php?success=false&error=consumo_excede&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

// Insertar registro en Registro_extras
$fecha = date('Y-m-d');
$hora = date('H:i:s');

$sql_insert = "INSERT INTO Registro_extras (fecha, hora, valor, producto, codigo_emp) VALUES (?, ?, ?, ?, ?)";
$params_insert = array($fecha, $hora, $valor, $producto, $codigo_emp);
$stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

if ($stmt_insert === false) {
    die("Error en la inserción de registro: " . print_r(sqlsrv_errors(), true));
}

// Actualizar saldo en Saldo_extras
$sql_update = "UPDATE Saldo_extras SET saldo = ? WHERE codigo_emp = ?";
$params_update = array($nuevo_saldo, $codigo_emp);
$stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

if ($stmt_update === false) {
    die("Error en la actualización del saldo: " . print_r(sqlsrv_errors(), true));
}

// Cerrar la conexión
sqlsrv_close($conn);

// Redirigir con éxito e incluir nombre, apellido y saldo en la URL
header("Location: ../index_extras.php?success=true&cedula_temp=" . urlencode($cedula_emp) . "&nombre_emp=" . urlencode($nombre_emp) . "&apellido_emp=" . urlencode($apellido_emp) . "&saldo=" . urlencode($nuevo_saldo));
exit();
?>
