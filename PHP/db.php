<?php
   // Conexión a SQL Server
$serverName = "localhost";
$uid = "sa";
$pwd = "faber33";
$databaseName = "dbcomedor1912";
$connectionInfo = array(
    "UID" => $uid,
    "PWD" => $pwd,
    "Database" => $databaseName,
    "TrustServerCertificate" => false,
    "Encrypt" => false,
    "CharacterSet" => "UTF-8"
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

?>