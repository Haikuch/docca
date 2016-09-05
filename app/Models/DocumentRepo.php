<?php
namespace App\Models;

class DocumentRepo {
    
    public static function getAllActive() {
        
        return DocumentRepoPdo::getActive();
    }
    
    public static function getOneById(int $docId) {
        
        $documents = DocumentRepoPdo::getById($docId);
        
        return $documents[0];
    }
    
    public static function getFiltered(FilterList $filters) {
        
        return DocumentRepoPdo::getFiltered($filters);
    }
    
    public static function getEmpty() {
                        
        $documentFactory = new DocumentFactory(); //TODO: evtl wird das ganz hierher verlagert
        
        return $documentFactory->make();
    }
   
    public static function save(Document $document) {
      
        return $docId = DocumentRepoPdo::save($document);
    }
    
    //
    public static function unlinkFileByFileId($fileIds) {
        
        return DocumentRepoPdo::deleteFileLinksByFileId($fileIds);
    }
    
    //
    public static function remove($docId) {
        
        return DocumentRepoPdo::remove($docId);
    }
}
