<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1,2,3]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="icon" href="./IMG/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .titulo {
            font-size: 24px;
            font-weight: bold;
        }
        .lista-reportes {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .item-reporte {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }
        .item-reporte:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            background-color: #f1f8ff;
        }
        .icono-reporte {
            font-size: 32px;
            color: #555;
            margin-right: 20px;
            transition: color 0.3s;
        }
        .item-reporte:hover .icono-reporte {
            color: #42a5f5;
        }
        .titulo-reporte {
            font-size: 18px;
            font-weight: 500;
        }
        .btn-return {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
        }
        .logo-fixed {
            position: fixed;
            top: 10px;
            right: 10px;
            width: 100px; /* Ajusta el tamaño del logo */
            height: auto; /* Mantén la proporción del logo */
            transition: top 0.3s ease;
        }
        /* Cambios para dispositivos móviles */
        @media only screen and (max-width: 900px) {
            .logo-fixed {
                display: none; /* Oculta el logo en dispositivos móviles */
            }
        }
        .logout-button {
            position: fixed;
            bottom: 70px;
            /* Ajusta la distancia desde la parte inferior */
            right: 20px;
            /* Ajusta la distancia desde la parte derecha */
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999;
            /* Asegúrate de que el botón esté sobre otros elementos */
        }

        .logout-button i {
            margin: 0;
        }

        .logout-button:hover {
            background-color: #ddd;
        }

        .logout-button:hover::after {
            content: "Cerrar sesión";
            position: absolute;
            bottom: 40px;
            right: 0;
            background-color: #333;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 9999;
        }
    </style>
</head>
<body>
<button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <button class="btn-return btn btn-primary" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>
    <img src="./IMG/logo.png" alt="Logo de la empresa" class="logo-fixed">
    <div class="container">
        <div class="header">
            <div class="titulo">Reportes</div>
        </div>
        <ul class="lista-reportes">
           <!-- <a href="tabla_Saldos.php" class="item-reporte">
                <i class="fas fa-wallet icono-reporte"></i>
                <div class="titulo-reporte">Saldos Acumulados</div>
            </a>-->
            <a href="saldos_fecha.php" class="item-reporte">
                <i class="fas fa-chart-line icono-reporte"></i>
                <div class="titulo-reporte">Saldos por fecha</div>
            </a>
            <a href="saldos_general_anActual.php" class="item-reporte">
                <i class="fas fa-user-check icono-reporte"></i>
                <div class="titulo-reporte">Reporte de Valores Mes Actual</div>
            </a>
            <a href="asistencias_Hoy.php" class="item-reporte">
            <i class="fa-solid fa-rectangle-list icono-reporte"></i>
                <div class="titulo-reporte">Asistencias</div>
            </a>
            <a href="reporte_ventas.html" class="item-reporte">
                <i class="fas fa-shopping-cart icono-reporte"></i>
                <div class="titulo-reporte">próximamente</div>
            </a>
            
        </ul>
    </div>
</body>
</html>
