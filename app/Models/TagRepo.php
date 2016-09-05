<?php
namespace App\Models;

class TagRepo {
    
    //
    public static function getById($tagId) {
                
        return TagRepoPdo::getById($tagId);
    }
    
    //
    public static function getByName($tagName) {
                
        $tags = TagRepoPdo::getByName($tagName);
        
        return $tags;
    }
    
    //
    public static function getIdByName($tagName) { 
                
        $tags = TagRepoPdo::getByName($tagName); 
        
        foreach ($tags as $tag) {
            
            $tagIds[] = $tag->getId();
        }
        
        return $tagIds;
    }
   
    public static function save(array $tags) {
      
        return TagRepoPdo::save($tags);
    }
    
    public static function getIdsByTagList($tagModels) { 
        
        $tagIds = [];
        foreach ($tagModels as $tagModel) {
            
            $tagIds[] = $tagModel->getId();
        }
        
        return $tagIds;
    }
    
    public static function getLowerNamesByTagList($tagModels) {
        
        $getNames = [];
        foreach ($tagModels as $tagModel) {
            
            $getNames[] = strtolower($tagModel->getName());
        }
        
        return $getNames;
    }
    
    public static function getNamesByTagList($tagModels) {
        
        $getNames = [];
        foreach ($tagModels as $tagModel) {
            
            $getNames[] = $tagModel->getName();
        }
        
        return $getNames;
    }
}
