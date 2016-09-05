<?php

require_once('patternmethods.php');

/**
 * Erzeugen von Formularelementen
 * 
 * Formularelemente k�nnen einzeln erzeugt und gestaltet werden
 * Zudem können automatisch schon vorhandene Daten in die Felder eingetragen und �berpr�ft werden
 */
class CheckPatterns extends PatternMethods {    
   
    private $data;
    private $patterns;
    private $elemName;  
    protected $pattern;
    
    
    public function setElemName($elemname) {
        
        $this->elemName = $elemname;
    }

    protected function getElemName() {
               
        $return = $this->elemName;
        
        return $return;
    }   

    public function setData($data) {
        
        $this->data = $data;
    }

    protected function getData($elemName) {
        
        return $this->data[$elemName];
    }
    
    protected function getValue() {
        
        return $this->getData($this->getElemName());
    } 

    protected function getPatterns() {
        
        return $this->patterns;
    }

    public function setPattern($patterns) {
        
        $this->patterns = $patterns;
    }
    
    private function setActualPattern($pattern) {
        
        $this->pattern = $pattern;
    }
       
    
    /**
     * Prüft ob diese Pattern-Methode existiert
     * 
     * @param type $method
     * @return type
     */
    private function patternMethodExists($method) {
        
        //Prüfen ob Methode existiert
        if (!method_exists('PatternMethods', $method)) {

            trigger_error('[fxfo-internal] pattern-method "'.$method.'" not found', E_USER_ERROR);
        }
        
        return true;
    }
    
    /**
     * Gibt das Resultat der Patternmethoden zurück
     * 
     * @return type
     */
    public function result() {
        
        foreach ($patterns = $this->getPatterns() as $pattern) {
            
            //Checken ob Methode überhaupt existiert
            if (!$this->patternMethodExists($pattern['method'])) {
                
                return false;
            }
            
            //Pattern-Daten für die Methoden verfügbar machen
            $this->setActualPattern($pattern);
            
            //Prüfmethode aufrufen
            $result = $this->$pattern['method']();            
            
            //Result invertieren wenn gewünscht
            if (isset($pattern['inverted']) AND $pattern['inverted']) {
                
                $result['server'] = !$result['server'];
                $result['client'] = '!('. $result['client'] . ')';
            }
            
            //Result speichern
            $return[] = $result;
        }
        
        return $return;
    }

}

?>