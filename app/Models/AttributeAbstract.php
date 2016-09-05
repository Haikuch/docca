<?php
namespace App\Models;

class AttributeAbstract {
    
    private $id;
    private $name;
    private $valueId;
    private $valueName;
    
    public function __construct($props) {
        
        $this->populate($props);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        
        if (empty($this->name)) {
            
            //
            if (empty($this->getId())) {
                
                throw new Exception('neither name nor id given');
            }
            
            $this->name = AttributeRepo::getNameByAttributeId($this->getId());
        }
        
        return $this->name;
    }
    
    public function getValueId() {
        return $this->valueId;
    }

    public function getValueName() {
        
        if (empty($this->valueName)) {
            
            //
            if (empty($this->getValueId())) {
                
                throw new Exception('neither valueName nor valueId given');
            }
            
            $this->valueName = AttributeRepo::getValueNameByValueId($this->getValueId());
        }
        
        return $this->valueName;
    }
    
    private function populate($data) {
        
        isset($data['id']) ? $this->id = $data['id'] : '';
        isset($data['name']) ? $this->name = $data['name'] : '';
        isset($data['valueId']) ? $this->valueId = $data['valueId'] : '';
        isset($data['valueName']) ? $this->valueName = $data['valueName'] : '';
    }    
}
