<?php
namespace App\Models;

class FilterReqtags extends FilterHastags {
    
    protected $name = 'reqtags';
    protected $value;
    
    public function __construct($value) {
        
        parent::__construct($value);
    }
}
