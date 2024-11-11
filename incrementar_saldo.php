<?php
include './PHP/Usuarios/check_access.php';

// Asegura que solo los usuarios con role_id 2 (editor) o 1 (admin) puedan acceder
checkAccess([1, 4]);

// Código para mostrar la página
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros Extra</title>
    <link rel="icon" href="./IMG/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Colores temáticos */
        :root {
            --main-color: #ff9f00;
            /* Color principal (amarillo-naranja) */
            --border-color: #ff6f00;
            /* Color del borde (naranja más oscuro) */
        }

        body {
            background-color: #f4f4f4;
            /* Fondo general de la página */
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .header {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 120px;
            /* Aumentado el tamaño del logo */
            display: block;
            margin: 0 auto;
        }

        .header h2 {
            color: var(--border-color);
            margin: 0;
            padding-top: 10px;
            /* Añadido para separar el logo del texto */
        }

        .form-control {
            border-radius: 25px;
            border-color: var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .hidden {
            display: none;
        }

        .alert {
            margin-top: 20px;
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

        #cedula_temp {
            display: none;
        }

        /* Estilos personalizados para SweetAlert2 */
        .alerta-grande {
            width: 90%;
            /* Ancho del 90% de la pantalla */
            max-width: 600px;
            /* Ancho máximo para pantallas grandes */
            font-size: 1.5rem;
            /* Tamaño de letra más grande */
            padding: 20px;
            /* Padding adicional para mayor espacio */
        }


        #diferencia {
            font-size: 2rem; /* Ajusta el tamaño a tu gusto */
            margin: 10px 0;
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
        }
    </style>
</head>

<body>
<button class="btn-return btn btn-primary" onclick="window.location.href='index.php'">
        <i class="fas fa-arrow-left"></i>
    </button>


    <button class="logout-button" onclick="window.location.href='/comedor/PHP/Usuarios/logout.php';">
        <i class="fas fa-door-open"></i>
    </button>
    <div class="container">
        <div class="header">
            <img src="./IMG/logo.png" alt="Logo"> <!-- Logo centrado en el contenedor -->
            <h2>Agragar Saldos</h2>
        </div>
        <form id="registroForm" method="post" action="./PHP/registro_saldo.php">
            <div class="form-group">
                <label for="cedula_emp">Cédula de Empleado:</label>
                <input type="text" class="form-control" id="cedula_emp" name="cedula_emp" required>
                <input type="text" id="cedula_temp" name="cedula_temp">
            </div>
            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="number" class="form-control" id="valor" name="valor" step="0.01" min="0" required>
            </div>
            <div class="bottom">
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        </form>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
// Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    const cedulaInput = document.getElementById('cedula_emp');
    const saldoDisplay = document.createElement('span');
    saldoDisplay.style.marginLeft = '10px'; // Aseguramos que esté alineado con el campo
    cedulaInput.parentNode.appendChild(saldoDisplay);

    // Cuando el usuario ingresa la cédula
    cedulaInput.addEventListener('input', function () {
        const cedula = cedulaInput.value.trim();
        if (cedula.length > 0) {
            // Hacer la consulta AJAX para obtener el saldo
            fetch('./PHP/consulta_saldo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cedula_emp=${cedula}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.saldo !== null) {
                    if (data.saldo == 0) {
                        // Si el saldo es 0, mostrar en rojo
                        saldoDisplay.textContent = 'Saldo: $0';
                        saldoDisplay.style.color = 'red';
                    } else {
                        // Si el saldo es mayor a 0, mostrar en verde
                        saldoDisplay.textContent = `Saldo: $${data.saldo}`;
                        saldoDisplay.style.color = 'green';
                    }
                } else {
                    saldoDisplay.textContent = 'Error al obtener saldo.';
                    saldoDisplay.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error al obtener el saldo:', error);
                saldoDisplay.textContent = 'Error al obtener saldo.';
                saldoDisplay.style.color = 'red';
            });
        } else {
            saldoDisplay.textContent = '';
        }
    });
});
</script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('cedula_emp').focus(); // Mantener el foco en el campo de cédula al cargar la página
    });

   // Parámetros de la URL
const urlParams = new URLSearchParams(window.location.search);

// Alerta de error
if (urlParams.get('success') === 'false') {
    const error = urlParams.get('error');
    let errorMessage = '';

    switch (error) {
        case 'cedula_no_encontrada':
            errorMessage = 'La cédula no se encontró en el sistema.';
            break;
        case 'error_actualizacion_saldo':
            errorMessage = 'Hubo un problema actualizando el saldo del empleado.';
            break;
        case 'error_insercion_saldo':
            errorMessage = 'No se pudo insertar el saldo inicial del empleado.';
            break;
        case 'saldo_no_encontrado':
            errorMessage = 'No se encontró un saldo para este empleado.';
            break;
        case 'saldo_insuficiente':
            errorMessage = 'El saldo insuficiente generará un saldo por debajo de -2. Requiere recarga.';
            break;
        case 'consumo_excede':
            errorMessage = 'Este consumo reducirá tu saldo a negativo.';
            break;
        default:
            errorMessage = 'Error desconocido.';
    }

    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
        timer: 4000,
        timerProgressBar: true,
        showConfirmButton: false,
        customClass: {
            popup: 'alerta-grande',
        }
    }).then(() => {
        const cedulaTemp = urlParams.get('cedula_temp');
        if (cedulaTemp) {
            window.location.href = `incrementar_saldo.php?cedula_temp=${cedulaTemp}`;
        }
    });
}

// Alerta de registro exitoso
if (urlParams.get('success') === 'true') {
    const nombreEmp = urlParams.get('nombre_emp');
    const apellidoEmp = urlParams.get('apellido_emp');
    const saldo = urlParams.get('saldo');

    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: `Operación realizada exitosamente para ${nombreEmp} ${apellidoEmp}. Saldo actualizado: $${saldo}`,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        customClass: {
            popup: 'alerta-grande',
        }
    }).then(() => {
        const cedulaTemp = urlParams.get('cedula_temp');
        if (cedulaTemp) {
            window.location.href = `incrementar_saldo.php?cedula_temp=${cedulaTemp}`;
        }
    });
}


// Confirmación de pago
function confirmarPago() {
    Swal.fire({
        icon: 'warning',
        title: 'Confirmación de Pago',
        text: '¿Deseas proceder con esta operación de saldo?',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'alerta-grande',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Lógica para procesar el pago
            Swal.fire({
                icon: 'success',
                title: 'Operación Confirmada',
                text: 'La operación de saldo se realizó correctamente.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'alerta-grande',
                }
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Operación Cancelada',
                text: 'La operación de saldo ha sido cancelada.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'alerta-grande',
                }
            });
        }
    });
}

</script>



</body>

</html>