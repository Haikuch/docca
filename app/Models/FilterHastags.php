<?php
namespace App\Models;

class FilterHastags extends FilterAbstract {
    
    protected $name = 'hastags';
    protected $value;
    private $tagList = [];
    
    public function __construct($value) {
        
        parent::__construct($value);
        
        $this->parseValueToTaglist();
    }
    
    //
    public function getTagList() {
        
        return $this->tagList;
    }
    
    //
    private function parseValueToTaglist() {
        
        $tagNames = explode(',', $this->value);
        
        $tagList = TagRepo::getByName($tagNames);
        
        $this->tagList = $tagList;
    }
    
    //
    public function getMarked() {
        
        return TagRepo::getNamesByTagList($this->getTagList());
    }
}
