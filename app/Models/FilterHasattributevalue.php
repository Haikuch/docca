<?php
namespace App\Models;

class FilterHasattributevalue extends FilterAbstract {
    
    protected $name = 'hasattributevalue';
    protected $value;
    private $attributeList = [];
    
    public function __construct($value) {
        
        parent::__construct($value);
        
        $this->parseValueToAttributelist();
    }
    
    //
    public function getAttributeList() {
        
        return $this->attributeList;
    }
    
    //
    private function parseValueToAttributelist() {
        
        $attributeValueNames = array_map('trim', explode(',', $this->value));
        
        foreach ($attributeValueNames as $attributeValueName) {
            
            $attributeValueName = array_map('trim', explode(':', $attributeValueName));
            
            $this->attributeList[] = AttributeRepo::getByNamePair($attributeValueName[0], $attributeValueName[1]);
        }
    }
    
    //
    public function getMarked() {
        
        return '';
    }
}
