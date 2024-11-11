<?php
include 'db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que los campos requeridos existan y no estén vacíos
    if (isset($_POST['tipoComida'], $_POST['fechaInicio'], $_POST['fechaFinal'], $_POST['valor']) &&
    !empty($_POST['tipoComida']) && !empty($_POST['fechaInicio']) && !empty($_POST['fechaFinal']) && !empty($_POST['valor'])) {

        
        // Obtener los datos del formulario
        $tipoComida = $_POST['tipoComida']; // Ej: "Desayuno Reforzado"
        $fechaInicio = $_POST['fechaInicio'];
        $fechaFinal = $_POST['fechaFinal']; // Cambiado de fechaFin a fechaFinal
        $valor = (float) $_POST['valor']; // Asegurarse de que sea un número

        // Extraer la primera palabra del campo tipoComida
        $tipoCom = explode(' ', $tipoComida)[0]; // Ej: "Desayuno"

        // Validar si ya hay más de dos programaciones con la misma fecha y valor
        $sqlCheckDuplicates = "SELECT COUNT(*) as total 
                                FROM [dbo].[Comidas_ref_for] 
                                WHERE tipoCom = ? 
                                AND ((fechaInicio <= ? AND fechaFinal >= ?) OR (fechaInicio <= ? AND fechaFinal >= ?))
                                AND Valor = ?";
        $paramsDuplicates = [$tipoComida, $fechaFinal, $fechaFinal, $fechaInicio, $fechaInicio, $valor];
        $stmtDuplicates = sqlsrv_query($conn, $sqlCheckDuplicates, $paramsDuplicates);

        if ($stmtDuplicates === false) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=db_error");
            exit();
        }

        $rowDuplicates = sqlsrv_fetch_array($stmtDuplicates, SQLSRV_FETCH_ASSOC);
        if ($rowDuplicates['total'] >= 2) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=duplicate_records");
            exit();
        }

        // Verificar si hay una programación en curso del mismo tipo de comida
        $sqlCheckOngoing = "SELECT COUNT(*) as total 
                            FROM [dbo].[Comidas_ref_for] 
                            WHERE tipoCom = ? 
                            AND ((fechaInicio <= ? AND fechaFinal >= ?) OR (fechaInicio <= ? AND fechaFinal >= ?))";
        $paramsOngoing = [$tipoComida, $fechaFinal, $fechaFinal, $fechaInicio, $fechaInicio];
        $stmtOngoing = sqlsrv_query($conn, $sqlCheckOngoing, $paramsOngoing);

        if ($stmtOngoing === false) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=db_error");
            exit();
        }

        $rowOngoing = sqlsrv_fetch_array($stmtOngoing, SQLSRV_FETCH_ASSOC);
        if ($rowOngoing['total'] > 0) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=ongoing_program");
            exit();
        }

        // Buscar el id_com correspondiente en la tabla HorariosComida
        $sqlIdCom = "SELECT id_com FROM [dbo].[HorariosComida] WHERE tipo LIKE ?";
        $params = [$tipoCom . '%']; // Usar LIKE para coincidir con la primera palabra
        $stmt = sqlsrv_query($conn, $sqlIdCom, $params);

        if ($stmt === false) {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=db_error");
            exit();
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $id_com = $row['id_com'];

            // Insertar los datos en la tabla Comidas_ref_for
            $sqlInsert = "INSERT INTO [dbo].[Comidas_ref_for] 
                          (tipoCom, fechaInicio, fechaFinal, Valor, id_com) 
                          VALUES (?, ?, ?, ?, ?)";
            $paramsInsert = [$tipoComida, $fechaInicio, $fechaFinal, $valor, $id_com];
            $stmtInsert = sqlsrv_query($conn, $sqlInsert, $paramsInsert);

            if ($stmtInsert === false) {
                sqlsrv_close($conn);
                header("Location: ../configurarS.php?msg=insert_error");
                exit();
            }

            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=success");
            exit();
        } else {
            sqlsrv_close($conn);
            header("Location: ../configurarS.php?msg=id_not_found");
            exit();
        }
    } else {
        // Redirigir con mensaje de error si faltan campos
        header("Location: ../configurarS.php?msg=missing_fields");
        exit();
    }
} else {
    // Redirigir si el método no es POST
    header("Location: ../configurarS.php?msg=invalid_request");
    exit();
}
?>
