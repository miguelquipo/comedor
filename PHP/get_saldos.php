<?php
include './db.php';


if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Ejecutar el procedimiento almacenado sp_ObtenerSaldos
$proc = "{call asistencia_dia_actual()}";
$stmt = sqlsrv_query($conn, $proc);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$saldos = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $saldos[] = $row;
}

sqlsrv_close($conn);
