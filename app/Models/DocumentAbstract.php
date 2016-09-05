<?php
namespace App\Models;

class DocumentAbstract {
    
    private $id;
    private $name;
    private $uploader;
    private $comment;
    private $sourceTime;
    private $uploadTime;
    private $active;
    
    private $tags = [];
    private $files = [];
    private $attributes = [];

    public function __construct(array $props) {

        //init time
        //TODO: sould use here maketime for timestamp use as well
        $this->setSourceTime( new \DateTime('01.01.1970') );
        $this->setUploadTime( new \DateTime('01.01.1970') );
        
        $this->populate($props);
    }
    //TODO: not unmutable any more
    public function setId(int $id) {
        $this->id = $id;
        return $this;
    }

    private function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    private function setUploader(string $uploader) {
        $this->uploader = $uploader;
        return $this;
    }

    private function setComment(string $comment) {
        $this->comment = $comment;
        return $this;
    }

    private function setSourceTime(\DateTimeInterface $sourceTime) {
        $this->sourceTime = $sourceTime;
        return $this;
    }

    private function setUploadTime(\DateTimeInterface $uploadTime) {
        $this->uploadTime = $uploadTime;
        return $this;
    }

    private function setActive(int $active) {
        $this->active = $active;
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getNameMarkedBy($markedWord, $before, $after) {
        
        if (empty($markedWord)) {
            
            return $this->getName();
        }
        
        return preg_replace('/(' . $markedWord . ')/i', $before . '$0' . $after, $this->getName());
    }

    public function getUploader() {
        return $this->uploader;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getCommentMarkedBy($markedWord, $before, $after) {
        
        if (empty($markedWord)) {
            
            return $this->getComment();
        }
        
        return preg_replace('/(' . $markedWord . ')/i', $before . '$0' . $after, $this->getComment());
    }

    public function getSourceTime() {
        return $this->sourceTime;
    }

    public function getUploadTime() {
        return $this->uploadTime;
    }

    public function getActive() {
        return $this->active;
    }
    

    public function getFiles() {
        return $this->files;
    }
    
    /**
     * return sorted taglist
     * 
     * @return type
     */
    public function getTags() {
        
        $unsortedTags = $this->tags;
        
        $tmp = [];
        foreach ($unsortedTags as $key => $tag) {
            
            $tmp[$key] = $tag->getName();
        }
        
        asort($tmp);
        
        $sortedTags = [];
        foreach ($tmp as $key => $name_dummy) {
         
            $sortedTags[$key] = $unsortedTags[$key];
        }
        
        return $sortedTags;
    }

    public function getAttributes() {
        return $this->attributes;
    }
    
    /**
     * fills in the received data
     * 
     * @param array $props
     */
    private function populate(array $props) {
        
        //set all properties that has a method
        foreach ($props as $key => $value) {
            
            $methodName = 'set' . ucfirst($key);
            
            if (!method_exists($this, $methodName)) {
                
                continue;
            }
            
            $this->{$methodName}($value);
        }
    }
    
    //
    public function getDataArray() {
        
        $data['id'] = $this->getId();
        $data['name'] = $this->getName();
        $data['comment'] = $this->getComment();
        $data['uploader'] = $this->getUploader();
        $data['source_time'] = $this->getSourceTime()->getTimestamp();
        $data['upload_time'] = $this->getUploadTime()->getTimestamp();
        $data['active'] = $this->getActive();
        #$data['tags'] = $this->getTags();
        #$data['files'] = $this->getFiles();
        
        return $data;
    }
    
    
    //
    private function setFiles($files) {
        
        $this->clearFiles();
        
        foreach ($files AS $file) {
            
            $this->addFile( $file );
        }
        
        return $this;
    }

    //
    private function clearFiles() {
        
        $this->files = [];
        
        return $this;
    }

    //
    private function addFile($file) {
        
        $this->files[] = $file;
        
        return $this;
    }
    

    
    //
    private function setTags($tags) {
        
        $this->clearTags();
        
        foreach ($tags AS $tag) {
            
            $this->addTag( $tag );
        }
        
        return $this;
    }
    
    //
    private function clearTags() {
        
        $this->tags = [];
        
        return $this;
    }

    //
    private function addTag($tag) {
        
        $this->tags[] = $tag;
        
        return $this;
    }
    

    
    //
    private function setAttributes($attributes) {
        
        $this->clearAttributes();
        
        foreach ($attributes AS $attribute) {
            
            $this->addAttribute( $attribute );
        }
        
        return $this;
    }
    
    //
    private function clearAttributes() {
        
        $this->attributes = [];
        
        return $this;
    }

    //
    private function addAttribute($attribute) {
        
        $this->attributes[] = $attribute;
        
        return $this;
    }
    
    /**
     * return true if the attribute is set and value is correct
     * 
     * @param type $attributeId
     * @param type $valueId
     * @return boolean
     */
    public function hasAttributeValue(int $attributeId, int $valueId) {
        
        foreach ($this->getAttributes() as $attribute) {
            
            if ($attribute->getId() == $attributeId AND $attribute->getValueId() == $valueId) {
                
                return true;
            }
        }
        
        return false;
    }
}
