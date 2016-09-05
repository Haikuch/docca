<?php

namespace App\Models;

/**
 * Description of DocumentHydrator
 *
 * @author calvin
 */
class DocumentHydrator {
    
    //
    public function hydrate($source) {
        
        $methodName = 'from' . $source['method'];
        
        return $this->{$methodName}($source['data']);
    }
    
    //
    public function fromPdoMulti($rows) {
        
        $docs = [];
        foreach ( $rows AS $row ) {
            
            //start new doc
            if (!isset($doc) OR $doc->id != $row->id) {
                
                isset($doc) ? $docs[$doc->id] = $this->DocumentFactory->create($doc) : ''; 
                
                $doc = new \stdClass();
                $tagexist = [];
                $fileexist = [];
            }
            
            $doc->id = $row->id;
            $doc->name = $row->name;
            $doc->comment = $row->comment;
            $doc->sourceTime = new \DateTime();
            $doc->sourceTime->setTimestamp($row->sourceTime);

            //add new tag
            if (!isset($tagexist[$row->tagId])) {
                
                $tag = new \stdClass();
                $tag->id = $row->tagId;
                $tag->name = $row->tagName;
                
                $doc->tags[] = $tag;
                $tagexist[$row->tagId] = true;
            }
            
            //add new file
            if (!isset($fileexist[$row->fileId])) {
                
                $file = new \stdClass();
                $file->id = $row->fileId;
                $file->name = $row->fileName;
                
                $doc->files[] = $file;
                $fileexist[$row->fileId] = true;
            }
        }
        
        return $docs;
    }
    
}
