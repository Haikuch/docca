<?php
namespace App\Models;

abstract class FilterListAbstract {
    
    private $filters = [];
    private $marked = [];
    
    public function __construct($filters) {
        
        $this->populate($filters);
    }
    
    //
    public function getAllOrEmpty() {
        
        if (empty($this->filters)) {
            
            return [new FilterNone()];
        }
        
        return $this->filters;
    }
    
    //
    private function populate($filters) {
        
        if (empty($filters)) {
            
            $filters = [];
        }
        
        $filterNames = Filterset::getFilterNames();
        
        foreach ($filters AS $name => $value) {
            
            $className = 'App\Models\Filter' . ucfirst($name);
            
            if (!in_array($name, $filterNames)) {
                
                continue;
            }
            
            $this->filters[$name] = new $className($value);
            $markedName = strpos($name, 'tags') ? 'tags' : $name; #TODO: dirty hack, muss werte hinzufügen wenn zweimal key tags kommt
            $this->marked[$markedName] = $this->filters[$name]->getMarked(); 
            
        }
    }
    
    //
    public function getMarked() {
        
        return $this->marked;
    }
}
