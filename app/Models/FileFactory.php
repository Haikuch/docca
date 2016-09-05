<?php

namespace App\Models;

/**
 * File-Factory
 */
class FileFactory {
    
    /**
     * takes file-data and returns a File object
     * 
     * @param array $fileData
     * @return \Document
     */
    public static function make($fileData) {
        
        return new File($fileData);
    }
}
