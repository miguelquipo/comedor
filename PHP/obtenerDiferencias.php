
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

// Verificar la conexión
if ($conn === false) {
    die(json_encode(['error' => 'Error de conexión: ' . print_r(sqlsrv_errors(), true)]));
}

// Establecer opciones para la conexión que aseguren el manejo de UTF-8
sqlsrv_configure('WarningsReturnAsErrors', 0); // Ignorar advertencias

// Consulta SQL para ejecutar el procedimiento almacenado
$sql = "EXEC sp_diferenciaSubcidios";
$stmt = sqlsrv_query($conn, $sql);

// Verificar si la consulta se ejecutó correctamente
if ($stmt === false) {
    die(json_encode(['error' => 'Error al ejecutar la consulta: ' . print_r(sqlsrv_errors(), true)]));
}

$result = array();

// Obtener los resultados y convertir los caracteres a UTF-8
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Convertir cada valor del arreglo a UTF-8 con mb_convert_encoding
    array_walk_recursive($row, function(&$value, $key) {
        if (is_string($value)) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto'); // Convertir el texto a UTF-8
        }
    });
    
    $result[] = $row;
}

// Liberar recursos
sqlsrv_free_stmt($stmt);

// Verificar si hay resultados antes de enviar la respuesta
if (empty($result)) {
    echo json_encode(['error' => 'No se encontraron datos']);
} else {
    // Establecer el tipo de contenido como JSON
    header('Content-Type: application/json');
    
    // Convertir los resultados a JSON
    $jsonData = json_encode($result, JSON_UNESCAPED_UNICODE); // Evitar que escape caracteres UTF-8
    
    // Verificar si hubo algún error al codificar JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        die(json_encode(['error' => 'Error al codificar JSON: ' . json_last_error_msg()]));
    }
    
    // Enviar la respuesta en formato JSON
    echo $jsonData;
}

// Cerrar la conexión
sqlsrv_close($conn);
