<?php
namespace App\Models;

class DocumentRepoRequest {
   
    public function __construct() {

        $this->DocumentFactory = new DocumentFactory();
    }
   
    public function getAll() {
        
        $data = $_POST;
        
        $docData = new \stdClass();
        $docData->name = $data['name'];
        $docData->comment = $data['comment'];
        $docData->uploader = 'Someone';
        $docData->active = 1;
        $docData->uploadTime = new \DateTime();
        $docData->uploadTime->setTimestamp(time());
        $docData->sourceTime = new \DateTime($data['sourceTime']);
        $docData->files = [];
        
        $tags = array_map('trim', explode(',', $data['tags']));
        foreach ($tags as $tagName) {
            
            $tag = new \stdClass();
            $tag->name = $tagName;
            $docData->tags[] = $tag;
        }
        
        return $this->DocumentFactory->create($docData);
    }
}
