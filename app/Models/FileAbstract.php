<?php
namespace App\Models;

class FileAbstract {
    
    private $id;
    private $name;
    
    private $tmpName;
    private $type;
    private $error;
    private $size;
    private $pageNumbers = 0;
    

    
    public function __construct($props) {
        
        $this->populate($props);
    }
    
    //TODO: no longer immutable
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    private function setName($name) {
        $this->name = $name;
        return $this;
    }

    private function setTmpName($tmpName) {
        $this->tmpName = $tmpName;
        return $this;
    }

    private function setType($type) {
        $this->type = $type;
        return $this;
    }

    private function setError($error) {
        $this->error = $error;
        return $this;
    }

    private function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function setPageNumbers($pageNumbers) {
        $this->pageNumbers = $pageNumbers;
        return $this;
    }
    
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function getTmpName() {
        return $this->tmpName;
    }

    public function getType() { //todo: is this safe?
        return $this->type;
    }

    public function getError() {
        return $this->error;
    }

    public function getSize() {
        return $this->size;
    }
    
    public function getPageNumbers() {        
        return $this->pageNumbers;
    }
    
    /**
     * returns the actual filename
     * 
     * @return type
     */
    public function getFileLinkName() {
        
        return $this->getId() . '_' . $this->getName();
    }
    
    /**
     * returns the name of the preview file
     * 
     * @return type
     */
    public function getPreviewLinkName($pageNumber) {
        
        return 'file_' . $this->getId() . '-page_' . $pageNumber;
    }
    
    /**
     * returns the name of the thumb file
     * 
     * @return type
     */
    public function getThumbLinkName($pageNumber) {
        
        return 'file_' . $this->getId() . '-page_' . $pageNumber;
    }
    
    
    
    private function populate($props) {
        
        //set all properties that has a method
        foreach ($props as $key => $value) {
            
            $methodName = 'set' . ucfirst($key);
            
            if (!method_exists($this, $methodName)) {
                
                continue;
            }
            
            $this->{$methodName}($value);
        }
    }
}
