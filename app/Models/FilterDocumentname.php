<?php
namespace App\Models;

class FilterDocumentname extends FilterAbstract {
    
    protected $name = 'documentname';
    protected $value;
    
    public function __construct($value) {
        
        parent::__construct($value);
    }
}
