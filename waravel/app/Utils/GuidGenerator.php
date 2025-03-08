<?php

namespace App\Utils;


/**
 * Clase para la generación de GUIDs (Identificadores Únicos Globales).
 *
 * Esta clase proporciona un método estático para generar un identificador único utilizando
 * caracteres alfanuméricos aleatorios. El GUID generado tiene una longitud fija de 11 caracteres.
 */
class GuidGenerator
{

    /**
     * Genera un identificador único aleatorio.
     *
     * El identificador tiene una longitud de 11 caracteres y se compone de letras mayúsculas,
     * minúsculas y números. El valor es aleatorio y se genera utilizando la función `random_int()`
     * para asegurar una mejor aleatoriedad.
     *
     * @return string El identificador único generado.
     */
    public static function generarId()
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';

        for ($i = 0; $i < 11; $i++) {
            $index = random_int(0, strlen($caracteres) - 1);
            $id .= $caracteres[$index];
        }

        return $id;
    }
}
