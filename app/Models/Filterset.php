<?php
namespace App\Models;

class Filterset {
    
    //
    public static function getFilterNames() {
        
        $filterNames = ['hastags', 'reqtags', 'documentname', 'documentcomment', 'sourcetime', 'hasattributevalue'];
        
        return $filterNames;
    }
    
    //
    public static function getList() {
        
        $filters = [];
        foreach (self::getFilterNames() as $filterName) {
            
            $className = 'App\Models\Filter' . ucfirst($filterName);
            
            //
            if (!class_exists($className)) { err($className);
                
                continue;
            }
            
            //TODO: ist das zulässig hier objekte zue rstellen?
            $filters[] = new $className('');
        }
        
        return $filters;
    }
}
