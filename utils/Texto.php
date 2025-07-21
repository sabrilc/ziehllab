<?php

namespace utils;

class Texto
{
    /**
     * Convierte un texto a UTF-8 solo si no lo está ya.
     *
     * @param string|null $texto Texto a convertir
     * @return string Texto en UTF-8
     */
    public static function encodeUtf8(?string $texto): string
    {
        if ($texto === null || $texto === '') {
            return '';
        }

        $encoding = mb_detect_encoding($texto, ['UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'Windows-1252'], true);

        // Si ya está en UTF-8, no se hace conversión
        if ($encoding === 'UTF-8') {
            return $texto;
        }

        // Convertir a UTF-8 desde su encoding original detectado
        return mb_convert_encoding($texto, 'UTF-8', $encoding ?: 'ISO-8859-1');
    }

    /**
     * Aplica trim de forma segura incluso si el valor es null o no es string.
     *
     * @param mixed $value El valor a limpiar
     * @param string $character_mask Opcional. Caracteres a eliminar.
     * @return string
     */
    public static function trim($value, string $character_mask = " \t\n\r\0\x0B"): string
    {
            return trim((string)($value ?? ''), $character_mask);

    }

    /**
     * Convierte texto UTF-8 a ISO-8859-1 para uso con FPDF (que no soporta UTF-8).
     * Evita errores por caracteres mal renderizados como "Ã±" en vez de "ñ".
     *
     * @param string|null $texto El texto original (posiblemente en UTF-8).
     * @return string Texto codificado en ISO-8859-1, listo para FPDF.
     */
    public static function encodeLatin1(?string $texto): string
    {
        if (empty($texto)) {
            return '';
        }

        // Detectar el encoding original de forma segura
        $encoding = mb_detect_encoding($texto, ['UTF-8', 'ISO-8859-1', 'ISO-8859-15', 'Windows-1252'], true);

        // Si no se detecta o no es ISO-8859-1, convertir a Latin1
        if ($encoding === false || stripos($encoding, 'ISO-8859-1') === false) {
            return mb_convert_encoding($texto, 'ISO-8859-1', $encoding ?: 'UTF-8');
        }

        // Ya está en ISO-8859-1, devolver tal cual
        return $texto;
    }



}