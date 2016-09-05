<?php

class PatternMethods {
    
    public static function true() {
        
        $return = true;
        
        return $return;
    }
    
    public static function false() {
        
        $return = false;
        
        return $return;
    }
    
    public static function numeric($data) {
        
        $return = is_numeric( str_replace( ",", ".", $data->value() )); 
        
        return $return;
    }
   
    public static function bool($data) {
        
        $return = is_bool($data->value()); 
        
        return $return;
    }
   
    public static function required($data) {
        
        $return = $data->value() ? true : false; 
        
        $data->setElement('this', 'required', true);
        
        return $return;
    }
   
    public static function min($data) { 
        
        $count = $data->getParam('0');
        
        $return = (mb_strlen( $data->value() ) >= $count); 
        $return['client'] = "$(this).val().length >= " . $count;
        
        return $return;
    }
   
    public static function max($data) { 
        
        $count = $data->getParam('0');
        
        $return = (mb_strlen( $data->value() ) <= $count); 
        
        return $return;
    }
   
    public static function email($data) {
        
        $return = filter_var($data->value(), FILTER_VALIDATE_EMAIL); 
        
        return $return;
    }
   
    public static function func($data) { 
        
        $funcName = $data->getParam('0');
        
        $customFunction = $funcName($data->value());
        
        //Wenn Funktion Array übergibt wird server als return genommen
        if (is_array($customFunction)) {
            
            $return = $customFunction['server']; 
        }
        
        //Wenn kein Array sondern Bool wird dieser als Rückgabewert von Überprüfung angenommen
        else if (is_bool($customFunction)) {
        
            $return = $customFunction; 
        }
        
        //Fehler, custom-Funktion übergibt falsche Werte
        else {
            
            trigger_error('[fxfo-internal] return of custom patternfunction "'.$funcName.'" is wrong formatted.', E_USER_ERROR);
        }
        
        #errjs($return);
        
        return $return;
    }

    public static function logic($data) {
        
        $logic  = $data->getParam('0');
        $value2 = $data->getParam('1');
        
        if ($logic == '==') {
            
            $return = ($data->value() == $value2);
        }
        
        else if ($logic == '!=') {
            
            $return = ($data->value() != $value2);
        }
        
        else if ($logic == '<=') {
            
            $return = ($data->value() <= $value2);
        }
        
        else if ($logic == '>=') {
            
            $return = ($data->value() >= $value2);
        }
        
        else if ($logic == '<') {
            
            $return = ($data->value() < $value2);
        }
        
        else if ($logic == '>') {
            
            $return = ($data->value() > $value2);
        }
        
        return $return;
        
    }
    
    public static function confirm($data) {
        
        $value2 = $data->getData($data->getParam('0'));
        
        $return = ($data->value() == $value2);
        
        return $return;
        
    }
    
}

?>