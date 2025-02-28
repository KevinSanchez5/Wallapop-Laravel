<?php

namespace App\Utils;

class GuidGenerator
{
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
