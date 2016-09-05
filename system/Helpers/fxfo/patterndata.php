<?php

require_once('patternmethods.php');

/**
 * Erzeugen von Formularelementen
 * 
 * Formularelemente k�nnen einzeln erzeugt und gestaltet werden
 * Zudem können automatisch schon vorhandene Daten in die Felder eingetragen und �berpr�ft werden
 */
class PatternData {    
   
    private $elements;
    private $data;
    private $elemName;
    private $params;
    
    public function setElemName($elemName) {
        
        $this->elemName = $elemName;
    }
   
    public function getElemName() {
        
        return $this->elemName;
    }
   
    public function setData($data) {
        
        $this->data = $data;
    }
   
    public function getData($elemName = 'ALL') {
        
        //Alle Daten ausgeben
        if ($elemName == 'ALL') {
            
            return $this->data;
        }
        
        //Daten eines Elements ausgeben
        if (isset($this->data[$elemName])) {
            
            return $this->data[$elemName];        
        }
        
        //Falls keine Daten vorhanden
        else {
            
            return NULL;
        }
    }
    
    /**
     * Parameter werden vor der Überprüfung jedes einzelnen Patterns/Method übergeben
     * 
     * @param type $params
     */
    public function setParams($params) {
        
        $this->params = $params;
    }
   
    /**
     * Parameter können in den Methods abgerufen werden
     * 
     * @param type $paramIndex
     * @return type
     */
    public function getParam($paramIndex = 'ALL') {
        
        //Alle Elemente ausgeben
        if ($paramIndex == 'ALL') {
            
            return $this->params;
        }
        
        //Falls Index vorhanden
        if (isset($this->params[$paramIndex])) {
            
            return $this->params[$paramIndex];
        }
        
        //Falls Index nich vorhanden
        else {
            
            return false;
        }
        
    }
   
    public function setElements($elements) {
        
        $this->elements = $elements;
    }
    
    /**
     * Elements für das Formular manipulieren
     * 
     * @param type $elemName
     * @param type $index
     * @param type $value
     */
    public function setElement($elemName, $index, $value) {
        
        //this -> aktueller ElemName
        $elemName = ($elemName == 'this') ? $this->getElemName() : $elemName;
        
        //Wert speichern
        $this->elements[$elemName][$index] = $value;
    }
   
    public function getElement($elemName = 'ALL') {
        
        //Alle Elemente ausgeben
        if ($elemName == 'ALL') {
            
            return $this->elements;
        }
        
        //Ein Elements ausgeben
        return $this->elements[$elemName];
    }
    
    public function value() {
        
        return $this->getData($this->getElemName());
    }
}

?>