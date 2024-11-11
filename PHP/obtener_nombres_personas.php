<?php
include 'db.php';

// Obtener la entrada de búsqueda
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

// Consulta para obtener nombres y cédulas que coincidan
$sql = "SELECT cedula_emp, CONCAT(NOMBRE_EMP, ' ', APELLIDO_EMP) AS nombre_completo 
        FROM personal 
        WHERE cedula_emp LIKE ? OR NOMBRE_EMP LIKE ? OR APELLIDO_EMP LIKE ?";

$params = array("%$searchTerm%", "%$searchTerm%", "%$searchTerm%");
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$nombres = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $nombres[] = [
        'value' => $row['cedula_emp'], // valor para el campo de cédula
        'label' => $row['nombre_completo'] // valor a mostrar en el autocompletado
    ];
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode($nombres);
?>
