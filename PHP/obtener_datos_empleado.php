<?php
include 'db.php';

// Obtener el número de cédula desde la solicitud
$cedula_temp = trim((string)$_GET['cedula_temp']);

// Verificar que la cédula no esté vacía
if (empty($cedula_temp)) {
    echo json_encode(['error' => 'Cédula no proporcionada']);
    exit;
}

// Log para ver el valor de la cédula
error_log("Cédula recibida: " . $cedula_temp);

// Preparar la consulta para obtener los datos del empleado
$sql = "SELECT NOMBRE_EMP, APELLIDO_EMP, nombre_efc 
        FROM personal 
        WHERE cedula_emp = ?"; // Reemplaza "Empleados" y "cedula_emp" por los nombres de tu tabla y campo

$params = array($cedula_temp);

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    $errors = print_r(sqlsrv_errors(), true);
    error_log($errors); // Registrar el error en el log
    echo json_encode(['error' => 'Error al ejecutar la consulta', 'details' => $errors]);
    exit;
}

// Obtener los datos
$data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Verificar si se encontraron resultados
header('Content-Type: application/json');

// Si se encontraron resultados, devolver en formato JSON
if ($data) {
    echo json_encode($data);
} else {
    // Si no hay datos, devolver un mensaje claro para evitar la respuesta vacía
    echo json_encode(['error' => 'No se encontraron resultados']);
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
