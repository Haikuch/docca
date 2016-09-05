<?php

namespace App\Models;

/**
 * Document-Factory
 */
class TagFactory {
    
    /**
     * takes tag-data and returns Tag object
     * 
     * @param array $tagData
     * @return \Tag
     */
    public static function make(array $tagData) {
        
        $tag = new Tag($tagData);
        return $tag;
    }
}
