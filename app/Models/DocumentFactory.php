<?php

namespace App\Models;

/**
 * Document-Factory
 */
class DocumentFactory {
    
    /**
     * takes document-data and returns a Document object
     * 
     * @param array $documentData
     * @return \Document
     */
    public static function make(array $documentData = array()) {
       
        $data = $documentData;
        
        //make sourceTime
        if (isset($data['sourceTime'])) {
            
            $data['sourceTime'] = static::makeTime($data['sourceTime']); #todo: static??
        }
        
        else {
            
            $data['sourceTime'] = self::makeTime(); //todo: should be external
        }

        //make uploadTime
        if (isset($data['uploadTime'])) {
            
            $data['uploadTime'] = static::makeTime($data['uploadTime']);
        }
        
        else {
            
            $data['uploadTime'] = self::makeTime(); //todo: should be external
        }
        
        //make list of tags
        $tags = []; 
        if (is_array($data['tags'])) {
            
            foreach ($data['tags'] AS $tag) {

                $tags[] = TagFactory::make($tag);
            }
        }
        $data['tags'] = $tags;

        //make list of attributes
        $attributes = []; 
        if (is_array($data['attributes'])) {
            
            foreach ($data['attributes'] AS $attribute) {

                $attributes[] = AttributeFactory::make(['id' => $attribute['id'], 'valueId' => $attribute['valueId']]);
            }
        }
        $data['attributes'] = $attributes;
         
        //make list of files
        $files = [];
        if (is_array($data['files'])) {
            
            foreach ($data['files'] AS $file) {
            
                $file = self::makeFile($file);

                $files[] = $file;
            }
        }     
        $data['files'] = $files;
               
        
        //create new Document
        $document = new Document($data); #err($document); die();
       
        return $document;
    }
    
    /**
     * takes file-data and returns File object
     * 
     * @param array $fileData
     * @return \File
     */
    public static function makeFile(array $fileData) {
        
        $file = FileFactory::make($fileData);
        return $file;
    }
    
    /**
     * returns DateTime Object for timestamp or datestring
     * 
     * @param type $time
     * @return \App\Models\DateTime
     */
    private static function makeTime($time = 0) {
        
        $intTime = intval($time);
                
        //int is read as timestamp
        //TODO: not nice working
        if ($intTime AND $intTime == $time) {
            
            $dateTime = new \DateTime();
            
            $dateTime->setTimestamp($time);
            
            return $dateTime;
        }
        
        //string is read as date-format
        else if (is_string($time)) {
            
            return new \DateTime($time);
        }
        
        //no readable date is given
        return new \DateTime('01.01.1970');
    }
}
