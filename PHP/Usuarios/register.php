<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_name = $_POST['role'];

    if (empty($username) || empty($password) || empty($confirm_password) || empty($role_name)) {
        echo "All fields are required!";
    } elseif ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Obtener role_id del rol especificado
        $sql_role = "SELECT role_id FROM Roles WHERE role_name = ?";
        $stmt_role = sqlsrv_query($conn, $sql_role, array($role_name));
        $role = sqlsrv_fetch_array($stmt_role, SQLSRV_FETCH_ASSOC);
        $role_id = $role['role_id'];

        $sql = "INSERT INTO Usuarios (username, password, role_id) VALUES (?, ?, ?)";
        $stmt = sqlsrv_prepare($conn, $sql, array($username, $hashed_password, $role_id));

        if (sqlsrv_execute($stmt)) {
            header("Location: /comedor/PHP/Usuarios/login.php");
            exit;
        } else {
            echo "Error: " . sqlsrv_errors();
        }
    } else {
        echo "Passwords do not match!";
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
    <title>Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
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
            background-image: url('../../A-IMG/rotate_flor.png');
            background-size: 150px 150px, 100px 100px;
            background-repeat: repeat, repeat;
            background-position: 0 0, 200px 200px;
            opacity: 0.05;
            z-index: -1;
            animation: moveBackground 30s linear infinite;
        }

        @keyframes moveBackground {
            0% {
                background-position: 0 0, 200px 200px;
            }
            100% {
                background-position: 100% 100%, calc(200px + 100%) calc(200px + 100%);
            }
        }

        .register-container {
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

        .login-link {
            margin-top: 20px;
            display: block;
            color: #007BFF;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .return-container {
            position: absolute;
            top: 10px;
            left: 30px;
        }

        .return-button {
            display: block;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: rgb(51, 56, 210);
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .return-button i {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .return-button:hover {
            background-color: rgba(107, 136, 131, 0.7);
            transform: scale(1.05);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="return-container">
        <a href="./login.php" class="return-button">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div class="register-container">
        <img src="../../IMG/logo.png" alt="Logo" class="logo">
        <form method="POST" action="register.php" onsubmit="return validateForm()">
            <input type="text" name="username" class="form-input" placeholder="Username" id="username" required>
            <select name="role" class="form-input" id="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
                <option value="phone">Phone</option>
            </select>
            <input type="password" name="password" class="form-input" placeholder="Password" id="password" required>
            <span id="passwordError" class="error-message"></span>
            <input type="password" name="confirm_password" class="form-input" placeholder="Confirm Password" id="confirm_password" required>
            <span id="confirmPasswordError" class="error-message"></span>
            <button type="submit" class="form-button">Registrar</button>
        </form>
        <a href="/comedor/PHP/Usuarios/login.php" class="login-link">¿Dispones de una cuenta? Click aquí para iniciar</a>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            const passwordRegex = /.{8,}/; // Mínimo de 8 caracteres

            let isValid = true;

            // Limpiar mensajes de error
            passwordError.textContent = '';
            confirmPasswordError.textContent = '';

            // Validar contraseña
            if (!passwordRegex.test(password)) {
                passwordError.textContent = "La contraseña debe tener al menos 8 caracteres.";
                isValid = false;
            }

            // Validar coincidencia de contraseñas
            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Las contraseñas no coinciden.";
                isValid = false;
            }

            return isValid;
        }

        // Validación en tiempo real
        document.getElementById('password').addEventListener('input', validateForm);
        document.getElementById('confirm_password').addEventListener('input', validateForm);
    </script>
</body>
</html>

