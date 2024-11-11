<?php
// Incluir archivo de conexión
include('db.php');
date_default_timezone_set('America/Chicago');

// Extraer y limpiar los datos del formulario
$cedula_emp = isset($_POST['cedula_emp']) ? trim($_POST['cedula_emp']) : '';
$valor = isset($_POST['valor']) ? (float)$_POST['valor'] : 0;

// Verificar si la cédula no está vacía
if (empty($cedula_emp)) {
    header("Location: ../incrementar_saldo.php?success=false&error=cedula_no_encontrada");
    exit();
}

// Validar conexión a la base de datos
if ($conn === false) {
    die("Error en la conexión: " . print_r(sqlsrv_errors(), true));
}

// Buscar el código de empleado, nombre y apellido usando la cédula
$sql_codigo_emp = "SELECT codigo_emp, NOMBRE_EMP, APELLIDO_EMP FROM personal WHERE LTRIM(RTRIM(CEDULA_EMP)) = ?";
$params_codigo_emp = array($cedula_emp);
$stmt_codigo_emp = sqlsrv_query($conn, $sql_codigo_emp, $params_codigo_emp);

if ($stmt_codigo_emp === false) {
    die("Error en la consulta de código de empleado: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt_codigo_emp, SQLSRV_FETCH_ASSOC);
if (!$row) {
    header("Location: ../incrementar_saldo.php?success=false&error=cedula_no_encontrada");
    exit();
}

$codigo_emp = $row['codigo_emp'];
$nombre_emp = $row['NOMBRE_EMP'];
$apellido_emp = $row['APELLIDO_EMP'];

// Verificar si ya existe un registro de saldo para el empleado
$sql_saldo = "SELECT saldo FROM Saldo_extras WHERE codigo_emp = ?";
$params_saldo = array($codigo_emp);
$stmt_saldo = sqlsrv_query($conn, $sql_saldo, $params_saldo);
$row_saldo = sqlsrv_fetch_array($stmt_saldo, SQLSRV_FETCH_ASSOC);

// Si ya existe un saldo, actualizarlo
if ($row_saldo) {
    $saldo_actual = $row_saldo['saldo'];
    $nuevo_saldo = $saldo_actual + $valor;

    // Actualizar el saldo
    $sql_update = "UPDATE Saldo_extras SET saldo = ? WHERE codigo_emp = ?";
    $params_update = array($nuevo_saldo, $codigo_emp);
    $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

    if ($stmt_update === false) {
        die("Error en la actualización del saldo: " . print_r(sqlsrv_errors(), true));
    }

} else {
    // Si no existe un saldo, crear un nuevo registro
    $sql_insert_saldo = "INSERT INTO Saldo_extras (codigo_emp, saldo) VALUES (?, ?)";
    $params_insert_saldo = array($codigo_emp, $valor);
    $stmt_insert_saldo = sqlsrv_query($conn, $sql_insert_saldo, $params_insert_saldo);

    if ($stmt_insert_saldo === false) {
        die("Error en la inserción de saldo: " . print_r(sqlsrv_errors(), true));
    }
    $nuevo_saldo = $valor; // Si es un nuevo registro, el nuevo saldo es el valor ingresado
}

// Cerrar la conexión
sqlsrv_close($conn);

// Redirigir con éxito, incluyendo el nombre, apellido y saldo actualizado en la URL
header("Location: ../incrementar_saldo.php?success=true&nombre_emp=" . urlencode($nombre_emp) . "&apellido_emp=" . urlencode($apellido_emp) . "&saldo=" . urlencode($nuevo_saldo));
exit();
?>
