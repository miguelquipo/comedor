<?php
include 'db.php';


// Obtener datos del formulario
$nombre_emp = strtoupper(trim($_POST['nombre_emp']));
$apellido_emp = strtoupper(trim($_POST['apellido_emp']));
$cedula_emp = trim($_POST['cedula_emp']);
$nombre_efc = trim($_POST['nombre_efc']);
$nueva_efc = strtoupper(trim($_POST['nueva_efc']));
$nombre_gfc = trim($_POST['nombre_gfc']);

// Validar si la cédula ya existe
$query = "SELECT COUNT(*) AS total FROM personal WHERE CEDULA_EMP = ?";
$params = [$cedula_emp];
$stmt = sqlsrv_query($conn, $query, $params);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($row['total'] > 0) {
    header("Location: ../ingreso_personal.php?success=false&error=cedula_existente&cedula_temp=" . urlencode($cedula_emp));
    exit();
}

// Generar codigo_emp único
do {
    $codigo_emp = rand(10000000, 99999999);
    $query = "SELECT COUNT(*) AS total FROM personal WHERE codigo_emp = ?";
    $stmt = sqlsrv_query($conn, $query, [$codigo_emp]);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
} while ($row['total'] > 0);

// Verificar si se ingresó una nueva área
if (!empty($nueva_efc)) {
    $nombre_efc = $nueva_efc;

    // Validar si el área ya existe
    $query = "SELECT COUNT(*) AS total FROM personal WHERE nombre_efc = ?";
    $stmt = sqlsrv_query($conn, $query, [$nombre_efc]);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row['total'] == 0) {
        // Generar codigo_efc único
        $codigo_efc = rand(10000000000000, 99999999999999);

        // Insertar nueva área
        $query = "INSERT INTO personal (nombre_efc, codigo_efc) VALUES (?, ?)";
        sqlsrv_query($conn, $query, [$nombre_efc, $codigo_efc]);
    } else {
        // Obtener el código existente
        $query = "SELECT codigo_efc FROM personal WHERE nombre_efc = ?";
        $stmt = sqlsrv_query($conn, $query, [$nombre_efc]);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $codigo_efc = $row['codigo_efc'];
    }
} else {
    // Obtener el código del área seleccionada
    $query = "SELECT codigo_efc FROM personal WHERE nombre_efc = ?";
    $stmt = sqlsrv_query($conn, $query, [$nombre_efc]);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $codigo_efc = $row['codigo_efc'];
}

// Insertar registro del empleado
$query = "INSERT INTO personal (codigo_emp, NOMBRE_EMP, APELLIDO_EMP, NOMBRE_EST, nombre_efc, codigo_efc, CEDULA_EMP, nombre_gfc) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$params = [$codigo_emp, $nombre_emp, $apellido_emp, 'Activo', $nombre_efc, $codigo_efc, $cedula_emp, $nombre_gfc];
$result = sqlsrv_query($conn, $query, $params);

if ($result) {
    header("Location: ../ingreso_personal.php?success=true&cedula_temp=" . urlencode($cedula_emp));
} else {
    header("Location: ../ingreso_personal.php?success=false&error=guardado_fallido&cedula_temp=" . urlencode($cedula_emp));
}
exit();
?>
