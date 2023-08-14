<?php
namespace App\Service\Utilities;

class ArrayService {

    /**
     * Get value of key in array
     * 
     * @param [] $array
     * @param string $key
     * @param ?mixed $default
     * 
     * @return null|string|array...
     */
    public static function getValOfKey(
        array $array, 
        string $key, 
        mixed $default=null
    ) : mixed
    {
        if(is_array($array) && $key){
            if(strpos($key, '|') > 0){
                list($field, $prop) = explode('|', $key);
                
                if(isset($array[$field][$prop]) && $array[$field][$prop]){
                    return $array[$field][$prop];
                }
            }
            else if(isset($array[$key]) && $array[$key]){
                return $array[$key];
            }
        }
        return $default;
    }
    
}
