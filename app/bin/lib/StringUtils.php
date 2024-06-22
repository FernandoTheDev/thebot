<?php

/**
 * Ayudar a manejar los strings
 */
class StringUtils
{

    final public static function RemoveStrings(?string $string)
    {
        return @preg_replace('/[^0-9]/', '', $string);
    }

    /**
     * Obtiene un string según el array del metodo $_GET o $_POST
     * Si no existe la key, se puede establecer un valor por defecto
     *
     * @param string $key Array key
     * @param string $def_value Valor por defecto
     * @param boolean $require Die if empty
     */
    final public static function GetQuery(string $key, ?string $def_value = 'key', bool $require = false): ?string
    {
        $value = @$_GET[$key] ?? @$_POST[$key] ?? null;
        if ($require && $value == null) {
            error_log('Fatal error: Missing key: ' . $key);
            die('Fatal error: Missing parameter: ' . $key);
        }
        return $value ?? $def_value;
    }

    /**
     * Limpiar un string a formato checker
     * 
     * @example 'CC Number | Exp | Cvv: 4264296186766413 | 09/2024 | 350 => 4264296186766413|09|2024|350
     * 
     * @param string $input String a limpiar
     * @param string $type Tipo de string a limpiar (cc o gen)
     */
    final public static function CleanString(string $input, string $type = 'gen'): string
    {
        $data = str_replace(['|', ':', '/', "\n", ' ', 'n', "\t", 'Expm', 'Expy', 'CCnum', 'CV2', 'CVV2'], '|', $input);
        $resultado = preg_replace('/\s+/', '', $data);

        if ($type == 'cc') {
            $resultado = preg_replace('/[^0-9]/', ' ', $resultado);
        } elseif ($type == 'gen') {
            $resultado = preg_replace('/[^0-9x]/', ' ', $resultado);
        } else {
            $resultado = preg_replace('/[^0-9]/', ' ', $resultado);
        }

        return preg_replace('/\s+/', '|', trim($resultado));
    }

    /**
     * Eliminar caracteres especiales para poder enviarlos por la api de Telegram
     *
     * @param string|null $string String a limpiar
     */
    final public static function QuitHtml(?string $string): ?string
    {
        return @str_replace(['<', '>', '≤', '≥'], ['&lt;', '&gt;', '&le;', '&ge;'], $string);
        // return @addslashes($tm);
    }

    /**
     * Eliminar caracteres markdown
     */
    final public static function QuitMarkdown(?string $string, string $replace = '\\'): ?string
    {
        $mark = ['*', '_', '__', '~', '||', '[', ']', '(', ')', '```', '`'];
        return @str_replace($mark, $replace, $string);
    }

    /**
     * Eliminar todos los caracteres no alfanuméricos y mantener los espacios
     */
    public static function RemoveNoAlpha(?string $string): ?string
    {
        $r = @preg_replace('/[^a-zA-Z0-9\s]/', ' ', $string);
        return @preg_replace('/\s+/', ' ', $r);
    }

    /**
     * Convierte saltos de linea html en saltos de linea en plain text
     */
    public static function ConvertHtmlToPlainText(?string $string): ?string {
        $saltos = ['</p>', '<p>','</br>', '<br>', '</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</h6>'];
        return @str_replace($saltos, "\n", $string);
    }

    /**
     * Eliminar los espacios de un string
     */
    public static function RemoveSpaces(?string $string): ?string {
        return @preg_replace('/\s+/', '', $string);
    }

    /**
     * Int time to string
     */
    public static function TimeToString($seconds, string $format = '%a days, %h hours, %i minutes and %s seconds'): string
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format($format);
    }
}