<?php
include 'db.php';

// Obtener subsidios del formulario
$subsidios = $_POST['subsidios'];

$success = true;

foreach ($subsidios as $tipo => $valor) {
    $sql = "UPDATE [dbo].[HorariosComida] SET subS = ? WHERE tipo = ?";
    $params = array($valor, $tipo);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $success = false;
        break;
    }
}

sqlsrv_close($conn);

if ($success) {
    header("Location: ../configurarS.php?success_S");
} else {
    header("Location: ../configurarS.php?error_S");
}
exit();
