<?php
namespace App\Models;

use \Helpers\Db;

class FileRepoPdo {
    
    public static function save(array $files) {
        
        foreach ($files as $key => $file) { 
            
            //insert
            if (empty($file->getId())) {
                
                $fileId = self::insert($file);
            }
            
            //update
            else {
                
                $fileId = self::update($file);
            }
            
            $files[$key]->setId($fileId);
        }
        
        #err($files); die();
        
        return $files;
    }
    
    //
    private function insert(File $file) {
        
        $data = [];
        $data['name'] = $file->getName();
        $data['mime'] = $file->getType();
        $data['size'] = $file->getSize();
        $data['pagenumbers'] = $file->getPageNumbers();
        
        Db::get()->insert("files", $data);
        
        return Db::get()->lastInsertId();
    }
    
    //
    private function update(File $file) {
        
        $data = [];
        $data['name'] = $file->getName();
        $data['mime'] = $file->getType();
        $data['size'] = $file->getSize();
        $data['pagenumbers'] = $file->getPageNumbers();
        
        Db::get()->update("files", $data, ['id' => $file->getId()]);
        
        return $file->getId();
    }
}
