<?php

namespace App\Helper;

class ArrayHelper
{
    /**
     * Obtiene un valor de un array de forma segura
     */
    public static function getValue(array $array, string $key, $default = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Obtiene un valor anidado de un array usando notación de puntos
     */
    public static function getNestedValue(array $array, string $path, $default = null)
    {
        $keys = explode('.', $path);
        $value = $array;
        
        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }
        
        return $value;
    }

    /**
     * Obtiene un valor de un array con validación de tipo
     */
    public static function getSafeValue(array $array, string $key, ?string $expectedType = null, $default = null)
    {
        if (!array_key_exists($key, $array)) {
            return $default;
        }
        
        $value = $array[$key];
        
        if ($expectedType !== null && gettype($value) !== $expectedType) {
            return $default;
        }
        
        return $value;
    }

    /**
     * Obtiene un valor de un array multidimensional
     */
    public static function getMultiValue(array $array, array $keys, $default = null)
    {
        $value = $array;
        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }
        
        return $value;
    }
} 