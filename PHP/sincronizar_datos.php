<?php
include 'db.php';

try {
    // Ejecutar el procedimiento almacenado
    $query = "{CALL SincronizarPersonal}";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        throw new Exception("Error al ejecutar el procedimiento almacenado.");
    }

    // Responder con éxito
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Responder con error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>