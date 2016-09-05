<?php
namespace App\Models;

class FileRepo {
   
    public static function save(array $files) {
      
        //save in DB and get ID
        $files = FileRepoPdo::save($files);
        
        //move file to disk and create images
        $files = FileRepoDisk::save($files);
        
        //update DB from disk operations
        $files = FileRepoPdo::save($files); #todo: only because of the pagenumbers 
        
        return $files;
    }
    
    public static function getIdsByFileList($fileModels) { 
        
        $fileIds = [];
        foreach ($fileModels as $fileModel) {
            
            $fileIds[] = $fileModel->getId();
        }
        
        return $fileIds;
    }
}
