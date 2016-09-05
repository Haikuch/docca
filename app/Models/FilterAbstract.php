<?php
namespace App\Models;

abstract class FilterAbstract {
    
    public function __construct($value) {
        
        $this->populate(['value' => $value]);
    }
    
    //
    public function getName() {
        
        return $this->name;
    }
    
    //
    public function getValue() {
        
        return $this->value;
    }
    
    //
    protected function setValue($value) {
        
        $this->value = $value;
    }
    
    //
    private function populate($props) {
        
        $this->setValue($props['value']);
    }
    
    //
    public function getValueFields() {
        
        $fields = '<input type="text" name="'.$this->getName().'" filtername="'.$this->getName().'" placeholder="' . \Language::show('placeholder_'.$this->getName().'', 'Filterbar') . '" value="'.$this->getValue().'">';
        
        return $fields;
    }
    
    //
    public function getMarked() {
        
        return $this->getValue();
    }
}
