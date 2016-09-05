<?php
namespace App\Models;

class FilterNone extends FilterAbstract {
    
    protected $name = 'none';
    protected $value;
    
    public function __construct() {
        
        parent::__construct(NULL);
    }
    
    //
    public function getValueFields() {
        
        $fields = '';
        
        return $fields;
    }
}
