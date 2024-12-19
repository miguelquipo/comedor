
<?php
include './PHP/Usuarios/check_access.php';

// Asegura que solo los usuarios con role_id 2 (editor) o 1 (admin) puedan acceder
checkAccess([1, 2]);

// Código para mostrar la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Comedor</title>
    <link rel="icon" href="./IMG/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Colores temáticos */
        :root {
            --main-color: #ff9f00; /* Color principal (amarillo-naranja) */
            --border-color: #ff6f00; /* Color del borde (naranja más oscuro) */
        }

        body {
            background-color: #f4f4f4; /* Fondo general de la página */
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
            max-width: 120px; /* Aumentado el tamaño del logo */
            display: block;
            margin: 0 auto;
        }

        .header h2 {
            color: var(--border-color);
            margin: 0;
            padding-top: 10px; /* Añadido para separar el logo del texto */
        }

        .form-control {
            border-radius: 25px;
            border-color: var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .nombre-container {
            padding: 10px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            background-color: #f9f9f9;
            font-weight: bold; /* Texto en negrita */
            text-align: center; /* Centrar el texto */
        }

        .nombre-container p {
            font-size: 1.2rem; /* Aumentar el tamaño del texto */
            margin: 10px 0; /* Espacio entre cada etiqueta p */
        }

        .hidden {
            display: none;
        }
        .alert {
            margin-top: 20px;
        }

        .diferencia-negativo {
            color: red;
        }

        .diferencia-entre-0-y-5 {
            color: blue;
        }

        .diferencia-mayor-a-5 {
            color: green;
        }

      
        #diferencia {
            font-size: 2rem; /* Ajusta el tamaño a tu gusto */
            margin: 10px 0;
        }
        .logout-button {
            position: fixed;
            bottom: 70px; /* Ajusta la distancia desde la parte inferior */
            right: 20px;  /* Ajusta la distancia desde la parte derecha */
            background-color: #f0f0f0;
            border: none;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 24px;
            color: #333;
            z-index: 9999; /* Asegúrate de que el botón esté sobre otros elementos */
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
#cedula_temp{
    display : none;
}
/* Estilos personalizados para SweetAlert2 */
.alerta-grande {
    width: 90%; /* Ancho del 90% de la pantalla */
    max-width: 600px; /* Ancho máximo para pantallas grandes */
    font-size: 1.5rem; /* Tamaño de letra más grande */
    padding: 20px; /* Padding adicional para mayor espacio */
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
            <h2>Registro de Asistencia al Comedor</h2>
        </div>
        <form id="registroForm" method="post" action="./PHP/registro.php">
            <div class="form-group">
                <label for="cedula_emp">Cédula de Empleado:</label>
                <input type="text" class="form-control" id="cedula_emp" name="cedula_emp" required>
                <input type="text" id="cedula_temp" name="cedula_temp">
            </div>
            <div class="form-group nombre-container hidden" id="nombreAreaContainer">
                <p id="nombreCompleto"></p>
                <p id="area"></p>
                <p id="diferencia" class="diferencia"></p>
            </div>
        </form>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
        async function obtenerDatos(cedulaEmp) {
            try {
                const response = await fetch(`./PHP/obtener_datos_empleado.php?cedula_temp=${cedulaEmp}`);
                console.log('Estado de respuesta:', response.status); // Agregar esto
                const data = await response.json();
                console.log('Datos obtenidos:', data); // Inspeccionar la respuesta
                return data;
            } catch (error) {
                console.error('Error al obtener los datos:', error);
                return null;
            }
        }

        // Función para mostrar los datos después del registro
        async function mostrarDatosPostRegistro(cedulaEmp) {
            const datos = await obtenerDatos(cedulaEmp);

            if (datos) {
                const nombreCompleto = `${datos.NOMBRE_EMP} ${datos.APELLIDO_EMP}`;
                const area = datos.nombre_efc;

                // Mostrar SweetAlert con el nombre, apellido y área
                Swal.fire({
                    icon: 'success',
                    title: 'Registro Exitoso',
                    text: `Nombre: ${nombreCompleto}\nÁrea: ${area}`,
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'alerta-grande', // Clase personalizada para todas las alertas
                    }
                });

                // Actualizar el contenedor en la página (si deseas mantenerlo)
                document.getElementById('nombreCompleto').textContent = `Nombre: ${nombreCompleto}`;
                document.getElementById('area').textContent = `Área: ${area}`;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontraron datos para la cédula proporcionada.',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'alerta-grande', // Clase personalizada
                    }
                });
            }
        }

        // Manejo de alertas con SweetAlert2
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'false') {
            const error = urlParams.get('error');
            let errorMessage = '';
            switch (error) {
                case 'guardado_fallido':
                    errorMessage = 'El registro no se guardó correctamente.';
                    break;
                case 'registro_existente_en_rango':
                    errorMessage = 'El trabajador ya tiene registro en este horario.';
                    break;
                case 'cedula_no_encontrada':
                    errorMessage = 'La cédula no se encontró en el sistema.';
                    break;
                case 'hora_fuera_de_rango':
                    errorMessage = 'La hora de registro está fuera del rango permitido.';
                    break;
                case 'tipo_comida_no_encontrado':
                    errorMessage = 'Tipo de comida no encontrado en el sistema.';
                    break;
                    case 'refrigerio_maximo':
                    errorMessage = 'Ha consumido todos sus refrigerios del día.';
                    break;
                default:
                    errorMessage = 'Error desconocido.';
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'alerta-grande', // Clase personalizada
                }
            }).then(() => {
                window.location.href = `index_asis.php?cedula_temp=${urlParams.get('cedula_temp')}`; // Redirigir a la página principal después de la alerta
            });
        } else if (urlParams.get('success') === 'true') {
            // Obtén la cédula temporal y muestra los datos
            const cedulaTemp = urlParams.get('cedula_temp');
            if (cedulaTemp) {
                mostrarDatosPostRegistro(cedulaTemp);
            }
        }

        // Manejo de la descarga del PDF
        if (urlParams.has('pdfFile')) {
            const pdfFile = urlParams.get('pdfFile');
            // Lógica para descargar el PDF
            window.location.href = `ruta/a/tu/directorio/${pdfFile}`; // Cambia la ruta según sea necesario
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('cedula_emp').focus(); // Mantener el foco en el campo de cédula al cargar la página
        });

        document.getElementById('cedula_emp').addEventListener('input', async (event) => {
            const cedulaEmp = event.target.value;

            if (cedulaEmp.length === 10) {
                // Enviar el formulario después de mostrar los datos
                document.getElementById('cedula_temp').value = cedulaEmp; // Almacenar el número de cédula en el campo oculto
                console.log('Cédula temporal:', cedulaEmp); // Verifica el valor de cédula_temp

                // Enviar el formulario
                document.getElementById('registroForm').submit();
            }
        });
    </script>


</body>
</html>
