<?php
/**
 * Array Helper Class
 *
 * @author Benjamin von Minden | http://pandory.de
 * @version 3.0
 */

namespace Helpers;

/**
 * Collection of array methods.
 */
class Arr
{
    /**
     * Sets an array value.
     *
     * @param array  $array
     * @param string $path
     * @param mixed  $value
     *
     * @return void
     */
    public static function set(array &$array, $path, $value)
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }
            $array =& $array[$segment];
        }
        $array[array_shift($segments)] = $value;
    }

    /**
     * Search for an array value. Returns TRUE if the array key exists and FALSE if not.
     *
     * @param array  $array
     * @param string $path
     *
     * @return bool
     */
    public static function has(array $array, $path)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if (!is_array($array) || !isset($array[$segment])) {
                return false;
            }
            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Returns value from array
     *
     * @param array  $array
     * @param string $path
     * @param mixed  $default
     *
     * @return array|null
     */
    public static function get(array $array, $path, $default = null)
    {
        $segments = explode('.', $path);
        foreach ($segments as $segment) {
            if (!is_array($array) || !isset($array[$segment])) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Remove an array value.
     *
     * @param   array  $array Array you want to modify
     * @param   string $path  Array path
     *
     * @return  boolean
     */
    public static function remove(array &$array, $path)
    {
        $segments = explode('.', $path);
        while (count($segments) > 1) {
            $segment = array_shift($segments);
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                return false;
            }
            $array =& $array[$segment];
        }
        unset($array[array_shift($segments)]);

        return true;
    }

    /**
     * Returns a random value from an array.
     *
     * @param   array $array Array you want to pick a random value from
     *
     * @return  mixed
     */
    public static function rand(array $array)
    {
        return $array[array_rand($array)];
    }

    /**
     * Returns TRUE if the array is associative and FALSE if not.
     *
     * @param   array $array Array to check
     *
     * @return  boolean
     */
    public static function isAssoc(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) === count($array);
    }

    /**
     * Returns the values from a single column of the input array, identified by the key.
     *
     * @param   array  $array Array to pluck from
     * @param   string $key   Array key
     *
     * @return  array
     */
    public static function value(array $array, $key)
    {
        return array_map(function ($value) use ($key) {
            return is_object($value) ? $value->$key : $value[$key];
        }, $array);
    }
    
    /**
     * returns an array, independent from input
     * 
     * @param mixed $var
     * @param bool $delimiter
     * @return
     */
    static public function make($var, $delimiter = false) {
        
        //if $var is object then decode it to array
        if (is_object($var)) {
            
            return json_decode(json_encode($var));
        }
        
        //if $var is string then create array
        if (is_string($var) OR is_numeric($var)) {
            
            //if $delimiter is given then explode the given string
            if ($delimiter) {
                
                return explode($delimiter, $var);
            }
            
            //return $var as array
            else {
                
                return array($var);
            }            
        }
        
        //if $var is already array, return this
        return $var;
    }
     
     
    /**
     * Läuft das Array durch und gibt $code mit %key und %value aus
     * 
     * @param type $code
     * @param type $array
     * @return type
     */
    public static function each($code, $array, $delimiter = '') {
        
        $return = '';
        
        foreach ($array AS $key => $value) {
            
            $return .= str_replace('%key',$key,str_replace('%value',$value,$code)).$delimiter;
        }
        
        //Delimiter wird wieder abgeschnitten
        if (strlen($delimiter) > 0) {

            return substr($return, 0, -strlen($delimiter));                
        }

        return $return;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * avoid()
     * 
     * Ein nicht-assoziatives Array mit nur einem Wert wird als String ausgegegeben. Ansonsten das Array.
     * 
     * @todo prüfen auf indiziert oder assoziativ
     * @param mixed $array          array das geprueft und zurueckgegeben wird
     * @param bool $explode=false   optional Delimiter angeben, dann wird jedes Array imploded
     * @return
     */
    static public function avoid($array, $implode = '') {
        
        //Falls kein Array einfach ausgeben
        if (!is_array($array)) {
            
            return $array;
            
        }
        
        //Falls Array nur einen Wert hat wird es in einen String gewandelt
        if (count($array) == 1) { 
            
            //Stringausgabe von einzelnem Wert
            return implode('', $array);            
            
        }
        
        //Falls mehrere Werte und Delimiter angegeben
        else {
            
            //Imploded String
            return implode($implode, $array);
            
        }
        
    }
    
    
    /**
     * Setzt ein Array zusammen nach dem Schema: $before.$value.$after
     * Wird kein Array übergeben wird $array einfach ausgegeben, $before und $after werden dann ignoriert
     * 
     * @param type $array
     * @param type $before
     * @param type $after
     * @return string
     */
    static function implode($array,$before="",$after="") {
	
	//Wird kein Array übergeben ist die Ausgabe $array
	if (!is_array($array)) {
	
		return $array;
	}
	
	//Wird ein Array übergeben wird dieses zusammengesetzt
	else {
	
            $newString='';

            foreach ($array AS $key => $value) {

                $newString .= $before.$value.$after;
            }		
	}
	
	//Der neue String wird ausgegeben
	return $newString;
    }    
    
    static function implodeMulti($delimiter, $key, $array) {
        
        #self::make($keys);

        $string = '';
        
        foreach ($array as $value) {
            
            $string .= $value->$key . $delimiter;
        }
        
        if (strlen($delimiter) > 0) {

            return substr($string, 0, -strlen($delimiter));                
        }

        else { 

            return $string;
        }        
    }
     
     /**
     * Gibt true bei assoziativem Array zurück, ansonsten false
     * 
     * @param type $array
     * @return type
     */
    static function isIndex($array) {
        
         if (is_array($array)) {
             
            return !is_array($array) && Arr::isAssoc($array) ? false : true;
         }
         
         else {
             
             return false;
         }
         
     }
     
     /**
      * Gibt True bei einem Multidimensionalen Array zurück
      * 
      * @param type $array
      * @return boolean
      */
     static function isMulti($array) {
        
        foreach ($array as $value) {
            
            if (is_array($value)) {
                
                return true;
            }       
        }
        
        return false;
    }
    
    /**
     * Einfache Template engine, ersetzt {key} mit assoziativem array
     * 
     * @todo: nur geklaute engine auf die gelinkt wird, irgendwann mal selbst zusammenbasteln
     * @param type $format
     * @param array $data
     * @return type
     */
    public static function format( $format, array $data) {
        
        $engine = new Engine();
        
        return $engine->render($format, $data);
    }
    
    /**
     * wie format() nur mit sprintf-test -> {key%s}
     * 3mal langsamer, aber springt zurück zu format wenn kein % gefunden
     * 
     * @todo: nur geklaute engine auf die gelinkt wird, irgendwann mal selbst zusammenbasteln
     * @param type $format
     * @param array $data
     * @return type
     */
    public static function sprintf( $format, array $data) {
        
        $engine = new SprintfEngine();
        
        return $engine->render($format, $data);
    }  
    
    /**
     * Gibt das Array $array ohne $value wieder
     * 
     * @param type $value
     * @param array $array
     */
    public static function deleteValue( $value, array $array) {
        
        unset( $array[array_keys( $array, $value, true )] );
    }  
}
