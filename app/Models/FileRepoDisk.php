<?php
namespace App\Models;

class FileRepoDisk {
    
    public static function save($files) {
        
        foreach ($files as $key => $file) {
            
            move_uploaded_file($file->getTmpName(), UPLOADDIR . $file->getFileLinkName());
            
            $file = self::createThumb($file);
            $file = self::createPreview($file);
            
            $files[$key] = $file;
        }
        
        return $files;
    }
    
    //
    private function createThumb(File $file) {
        
        $img = new \Imagick(UPLOADDIR . $file->getFileLinkName());
        
        for($i = 0;$i < $img->getNumberImages(); $i++) {
            
            $img->setiteratorindex($i);
        
            $img->scaleImage(70,0);
            $img->cropImage(70,95,0,0);
            
            $img->writeimage(THUMBDIR . $file->getThumbLinkName($i) . '.jpg');
        }
        
        $file->setPageNumbers($img->getNumberImages()); #not the best place for code,, die ganze imagick sache sollte vielleciht ne eigene methode haben die nur ein objekt erstellt
        
        return $file;
    }
    
    //
    private function createPreview(File $file) {
        
        $img = new \Imagick(UPLOADDIR . $file->getFileLinkName());
        
        for($i = 0;$i < $img->getNumberImages(); $i++) {
            
            $img->setiteratorindex($i);
        
            $img->scaleImage(600,0);
            
            $img->writeimage(PREVIEWDIR . $file->getPreviewLinkName($i) . '.jpg');
        }
        
        return $file;
    }
}
