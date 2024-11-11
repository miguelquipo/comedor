<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, role_id FROM Usuarios WHERE username = ?";
    $stmt = sqlsrv_query($conn, $sql, array($username));

    if ($stmt) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['logged_in'] = true;

            sqlsrv_query($conn, "UPDATE Usuarios SET session_active = 1 WHERE id = ?", array($user['id']));

            // Redirección según el rol
            switch ($user['role_id']) {
                case 1:
                    header("Location: /comedor/index.php");
                    break;
                case 2:
                    header("Location: /comedor/index.php");
                    break;
                case 3:
                    header("Location: /comedor/index.php");
                    break;
                case 4:
                    header("Location: /comedor/index.php");
                    break;
               }
            exit;
        } else {
            echo "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../IMG/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f0f0f0;
            margin: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
            position: relative;
            background-color: #f0f0f0;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../../IMG/logo.png');
            background-size: 150px 150px, 100px 100px; /* Tamaño de cada imagen */
            background-repeat: repeat, repeat; /* Repite las imágenes */
            background-position: 0 0, 200px 200px; /* Separación entre imágenes */
            opacity: 0.05;
            z-index: -1;
            animation: moveBackground 30s linear infinite;
        }



        @keyframes moveBackground {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100% 100%;
            }
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            position: relative;
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .form-input {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-button {
            width: 100%;
            background-color: #007BFF;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .form-button:hover {
            background-color: #0056b3;
        }

        .register-link {
            margin-top: 20px;
            display: block;
            color: #007BFF;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }
        /*Return Buttom*/
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
    <div class="background"></div>
    <div class="return-container">
    <a href="../../unauthorized.php" class="return-button">
      <i class="fas fa-arrow-left"></i>
    </a>
  </div>

    <div class="login-container">
        <img src="../../IMG/logo.png" alt="Logo" class="logo">
        <form method="POST" action="login.php">
            <input type="text" name="username" class="form-input" placeholder="Username" required>
            <input type="password" name="password" class="form-input" placeholder="Password" required>
            <button type="submit" class="form-button">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
