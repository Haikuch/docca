<?php
namespace App\Models;

class AttributeRepo {
    
    //
    public static function getActive() {
                
        return AttributeRepoPdo::getActive();
    }
    
    //
    public static function getNameByAttributeId($attributeId) {
        
        return AttributeRepoPdo::getNameByAttributeId($attributeId);
    }
    
    //
    public static function getValueNameByValueId($valueId) {
        
        return AttributeRepoPdo::getValueNameByValueId($valueId);
    }
    
    //
    public function getValuesByAttributeId($attributeId) {
        
        return AttributeRepoPdo::getValuesByAttributeId($attributeId);
    }
    
    //
    public static function getByNamePair($attributeName, $valueName) {
        
        return AttributeRepoPdo::getByNamePair($attributeName, $valueName);
    }
}
