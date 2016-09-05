<?php

namespace App\Models;

/**
 * Attribute-Factory
 */
class AttributeFactory {
    
    /**
     * takes attribute-data and returns Attribute object
     * 
     * @param array $data
     * @return \Attribute
     */
    public static function make(array $data) {
        
        $tag = new Attribute($data);
        return $tag;
    }
}
