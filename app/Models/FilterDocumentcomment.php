<?php
namespace App\Models;

class FilterDocumentcomment extends FilterAbstract {
    
    protected $name = 'documentcomment';
    protected $value;
    
    public function __construct($value) {
        
        parent::__construct($value);
    }
}
