<?php

abstract class MyDocument_Abstract {
    
    public function __construct($fileReference, array $tags = []) {
        
        $this->setFileReference($fileReference);
        $this->setTags($tags);
    }

    public function clearTags() {
        
        $this->tags = [];
        
        return $this;
    }

    public function addTag(MyTag $tag) {
        
        $this->tags[] = $tag;
        return $this;
    }

    public function setTags(array $tags) {
        
        $this->clearTags();
        
        foreach ($tags AS $tag) {
            
            $this->addTag( $tag );
        }
        
        return $this;
    }
}

class MyDocument extends MyDocument_Abstract {
    
    public function __construct($fileReference, array $tags = array()) {
        
        parent::__construct($fileReference, $tags);
    }    
    
    public function getId() {
        
        
    }
}

class MyDocument extends MyDocument_Abstract {
    protected $id;

    public function __construct($id, $fileReference, array $tags = []) {
        
        $this->setId($id);
        
        parent::__construct($fileReference, $tags);
    }

    public function setId($id) {
        
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}

abstract class MyTag_Abstract {
    protected $name;

    public function __construct($name) {
        $this->setName($name);
    }

    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
}

class MyTag extends MyTag_Abstract {
    protected $id;

    public function __construct($id, $name) {
        
        parent::__construct($name);
        $this->setId($id);
    }

    public function setId($id) {
        
        $this->id = $id;
        return $this;
    }

    public function getId() {
        
        return $this->id;
    }
}


class MyDocument_Factory {
    
    public function createDocument(array $props) {
        
        $doc = new MyDocument($props['file_reference']);
       
        foreach ($props['tags'] AS $tag) {
            $tag = $this->createTag($tag);
            $doc->addTag($tag);
        }
       
        return $doc;
    }
   
    public function createTag(array $props) {
        $tag = new MyTag_Mysql($props['id'], $props['name']);
        return $tag;
    }
}

class MyDocument_Repository_Mysql {
    protected $db;
    protected $factory;
   
    public function __construct(mysqli $db) {
        $this->db = $db;
        $this->factory = new MyDocument_Factory_Mysql();
    }
   
    public function findOneByFileReference($fileReference) {
        $query = sprintf(
            "SELECT file_reference, name FROM documents AS d
             INNER JOIN document_tags AS dt ON dt.document_id = d.id
             WHERE d.file_reference = '%s'",
            $this->db->real_escape_string( $fileReference )
        );
        $result = $this->db->query($query);
        $doc = [];
        while ( $row = $result->fetch_assoc() ) {
            $doc['file_reference'] = $row['file_reference'];
            $doc['tags'][] = $row['tag'];
        }
       
        return $this->getFactory()->createDocument($doc);
    }
   
    public function save(MyDocument_Mysql $doc) {
        if ( $doc->getId() === null ) {
            // insert
        }
        else {
            // update
        }
    }
}