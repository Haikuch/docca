<?php
namespace App\Controllers;

use Core\Controller;

class FileController extends Controller
{
    private $path_upload;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->fileModel = new \App\Models\FileModel();
        $this->path_upload = ROOTDIR . 'upload/';
    }
    
    public function upload($docId, $files) 
    {
        foreach ($files['tmp_name'] as $key => $tmp_name) {
        
            move_uploaded_file($tmp_name, $this->path_upload . $files['name'][$key]);   
            
            $filenames[] = $files['name'][$key];
        }
        
        $fileIds = $this->fileModel->save($docId, $filenames);
        
        return $fileIds;
    }
    
    public function deleteByFileId($fileId) {
        
        $files = $this->fileModel->getByFileId($fileId);
        
        foreach ($files as $file) {
            
            unlink($this->path_upload . $file->name);
        }
        
        //delete DB
        $this->fileModel->deleteByFileId($fileId);
    }
    
    public function deleteByDocId($docId) {
        
        $files = $this->fileModel->getByDocId($docId);
        
        foreach ($files as $file) {
            
            unlink($this->path_upload . $file->name);
        }
        
        //delete DB
        $this->fileModel->deleteByDocId($docId);
    }
}
