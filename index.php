<?php
include './PHP/Usuarios/check_access.php';
checkAccess([1,2,3,4]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Opciones Decorado</title>
    <link rel="icon" href="./IMG/logo.png">
    <!-- Incluye Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .contenedor {
            display: flex;
            gap: 20px;
            flex-wrap: wrap; /* Permite que las tarjetas se ajusten en pantallas pequeñas */
            justify-content: center; /* Alinea las tarjetas en el centro */
        }
        .card {
            width: 150px;
            height: 180px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
            cursor: pointer;
            overflow: hidden;
            text-decoration: none;
            margin-bottom: 10px; /* Espacio entre las tarjetas cuando se apilan */
        }
        .icono {
            font-size: 48px;
            color: #555;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        .titulo {
            font-size: 16px;
            color: #333;
            text-align: center;
        }
        /* Animación de Rebote */
        .card:hover .icono {
            animation: bounce 0.5s infinite alternate;
        }
        @keyframes bounce {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-10px);
            }
        }
        /* Colores específicos para cada tarjeta en hover */
        .asistencia:hover {
            background-color: #ff6f61; /* Rojo claro */
        }
        .asistencia:hover .icono {
            color: #ffffff; /* Icono blanco */
        }
        .reportes:hover {
            background-color: #42a5f5; /* Azul */
        }
        .reportes:hover .icono {
            color: #ffffff; /* Icono blanco */
        }
        .ingresos:hover {
            background-color: #66bb6a; /* Verde */
        }
        .ingresos:hover .icono {
            color: #ffffff; /* Icono blanco */
        }
        .configuracion:hover {
            background-color: #ffa726; /* Naranja */
        }
        .configuracion:hover .icono {
            color: #ffffff; /* Icono blanco */
        }
        .recargas:hover {
            background-color: #d4d412; 
        }
        .recargas:hover .icono {
            color: #ffffff; /* Icono blanco */
        }
        .logout-button {
            position: fixed;
            bottom: 70px;
            right: 20px;
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999;
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
        .logo {
            position: absolute;
            top: 1px;
            right: 70px;
            width: 130px;
            height: auto;
        }

        .logo-fixed {
            position: fixed;
            top: 10px;
            right: 10px;
            transition: top 0.3s ease;
        }
        /* Cambios para dispositivos móviles */
        @media only screen and (max-width: 900px) {
            .logo {
                display: none; /* Oculta el logo en dispositivos móviles */
            }
            .contenedor {
                flex-direction: column; /* Cambia la disposición a columna en pantallas pequeñas */
                align-items: center;
            }
            .card {
                width: 100%; /* Hace que las tarjetas ocupen el ancho completo en pantallas pequeñas */
                max-width: 300px; /* Limita el ancho máximo */
                margin-bottom: 20px; /* Agrega espacio entre las tarjetas */
            }
            .logout-button {
                bottom: 20px; /* Ajusta la posición en pantallas pequeñas */
                right: 20px;
            }
        }

    </style>

</head>
<body>
<img src="./IMG/logo.png" alt="Logo de la empresa" class="logo">
<button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <div class="contenedor">
        <a href="index_asis.php" class="card asistencia">
            <i class="fas fa-check-circle icono"></i>
            <div class="titulo">Ingresos</div>
        </a>
        <a href="list_reports.php" class="card reportes">
            <i class="fas fa-chart-bar icono"></i>
            <div class="titulo">Reportes</div>
        </a>
        <a href="index_extras.php" class="card ingresos">
            <i class="fas fa-dollar-sign icono"></i>
            <div class="titulo">Ingresos Extras</div>
        </a>
        <a href="incrementar_saldo.php" class="card recargas">
        <i class="fa-solid fa-money-bill icono"></i>
            <div class="titulo">Recarga Extras</div>
        </a>
        <a href="configurarS.php" class="card configuracion">
            <i class="fas fa-cog icono"></i>
            <div class="titulo">Configuración /<br>Programación</div>
        </a>
    </div>
</body>
</html>
