<?php
include 'db.php'; // Asegúrate de que esto apunta a tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_reffor']) && !empty($_POST['id_reffor'])) {
        $id_reffor = $_POST['id_reffor'];

        // Eliminar el registro
        $sqlDelete = "DELETE FROM [dbo].[Comidas_ref_for] WHERE id_reffor = ?";
        $paramsDelete = [$id_reffor];
        $stmtDelete = sqlsrv_query($conn, $sqlDelete, $paramsDelete);

        if ($stmtDelete === false) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=delete_error");
            exit();
        }

        sqlsrv_close($conn);
        header("Location: ../configurarS.php?msg=delete_success");
        exit();
    } else {
        header("Location: ../configurarS.php?msg=missing_id");
        exit();
    }
} else {
    header("Location: ../configurarS.php?msg=invalid_request");
    exit();
}
?>
