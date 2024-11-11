<?php
include './db.php';

if (isset($_POST['cedula_emp'])) {
    $cedula_emp = $_POST['cedula_emp'];

    // Consulta para obtener el saldo
    $sql = "
        SELECT S.saldo 
        FROM Saldo_extras S
        INNER JOIN Personal P ON P.codigo_emp = S.codigo_emp
        WHERE P.cedula_emp = ?
    ";

    // Preparamos la consulta y pasamos el parámetro de cédula
    $params = array($cedula_emp);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));  // Mostramos los errores si la consulta falla
    }

    $saldo = 0; // Inicializamos con 0 como valor predeterminado

    // Verificamos si la consulta devuelve algún resultado
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $saldo = $row['saldo'];
    }

    // Si no se encontró un saldo, el valor permanece en 0 (que es el valor predeterminado)
    echo json_encode(array('saldo' => $saldo));
}
?>
