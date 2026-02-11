<?php
/**
 * Clase para manejar la internacionalización
 */
class I18n
{

    private static $idioma = 'es'; ///< Idioma por defecto de los mensajes
    private static $locales = [ ///< Mapeo de idioma a locales
        'es' => 'es_CL.utf8',
        'en' => 'en_US.utf8',
    ];

    public static function setIdioma($idioma = 'es')
    {
        self::$idioma = $idioma;
    }

    /**
     * Método que realiza la traducción de un string a otro idioma.
     */
    public static function translate($string, $domain = 'master')
    {
        if (!isset(self::$locales[self::$idioma]) or !function_exists('gettext')) {
            return $string;
        }
        $locale = self::$locales[self::$idioma];
        putenv("LANG=".$locale);
        setlocale(LC_MESSAGES, $locale);
        bindtextdomain($domain, dirname(dirname(__FILE__)).'/locale');
        textdomain($domain);
        bind_textdomain_codeset($domain, 'UTF-8');
        return gettext($string);
    }
}
