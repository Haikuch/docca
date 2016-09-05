<?php
namespace App\Models;

class TagAbstract {
    
    private $id;
    private $name;

    public function __construct($props) {
        
        $this->setData($props);
    }
    
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    //
    private function setData($data) {
        
        isset($data['id']) ? $this->id = $data['id'] : '';
        
        //
        if (!isset($data['name'])) {
            err($data);
            throw new \Exception('tag needs a name');
        }
        
        $this->name = $data['name'];
    }    
}
