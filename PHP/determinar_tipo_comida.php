<?php
// determinar_tipo_comida.php

/**
 * Función para determinar el tipo de comida basado en la hora actual
 * 
 * @param string $horaActual Hora actual en formato 'H:i:s'
 * @return string|null Retorna el tipo de comida o null si está fuera de rango
 */
function determinarTipoComida($horaActual) {
    // Incluir el archivo de configuración con los horarios
    $horarios = include 'config_horarios.php';

    // Recorremos el array de horarios
    foreach ($horarios as $rango) {
        // Comprobamos si la hora actual está dentro del rango de la comida
        if ($horaActual >= $rango['inicio'] && $horaActual <= $rango['fin']) {
            return $rango['tipo']; // Aquí devolvemos el tipo de comida
        }
    }

    // Hora fuera de rango
    return null;
}
