<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;
use \Helpers\Request;
use \App\Models;

class ListController extends Controller {
    
    public function __construct() {
        
        parent::__construct();
        
        $this->language->load('Main');
        $this->language->load('Document');
    }
    
    public function documentIndex() {
        
        
    }
    
   
    
    
}
