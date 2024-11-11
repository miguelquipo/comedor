<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./IMG/logo_prueba.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <title>Access Denied</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-name-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px; /* Separation between the logo-name group and the message */
        }

        .logo {
            width: 150px;
            animation: rotate 5s linear infinite;
            margin-right: 20px; /* Separation between logo and name */
        }

        .name {
            width: 300px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .message {
            font-size: 24px;
            color: #333;
        }

        .login-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            color: white;
            background-color: #4CAF50; /* Light green */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #388E3C; /* Darker green */
        }
        .return-container {
            position: absolute;
            top: 10px;
            left: 30px;
        }
        .return-button {
            display:block;
            flex-direction:column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none; /* Eliminar subrayado */
            color: rgb(51, 56, 210); 
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .return-button i {
            font-size: 30px; /* Reducir tamaño del icono */
            margin-bottom: 10px;
        }

        .return-button:hover {
            background-color: rgba(107, 136, 131, 0.7); /* Color de fondo semi-transparente al hacer hover */
            transform: scale(1.05); /* Aumentar tamaño al hacer hover */
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        .return-button:hover span {
            backdrop-filter: none;
            filter: none;
        }
    </style>
</head>
<body>
<div class="return-container">
    <a href="index.php" class="return-button">
      <i class="fas fa-arrow-left"></i>
    </a>
  </div>
    <div class="container">
        <div class="logo-name-container">
            
            <img src="./IMG/logo.png" alt="Nombre" class="name">
        </div>
        <div class="message">La página a la que estás intentando acceder no está disponible para ti en este momento, retroceda la pagina o inicia sesión con otra cuenta.</div>
        <a href="./PHP/Usuarios/login.php" class="login-button">Iniciar sesión</a>
    </div>
</body>
</html>
