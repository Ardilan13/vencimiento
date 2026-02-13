<?php
// app/config/Env.php

class Env {
    private static $loaded = false;
    private static $variables = [];

    public static function load($path = null) {
        if (self::$loaded) return;

        if ($path === null) {
            $path = dirname(dirname(dirname(__FILE__))) . '/.env';
        }

        if (!file_exists($path)) {
            throw new Exception("Archivo .env no encontrado en: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remover comillas si existen
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }

            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        return self::$variables[$key] ?? $_ENV[$key] ?? $default;
    }

    public static function all() {
        return array_merge(self::$variables, $_ENV);
    }
}
?>
