<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;
use \Helpers\Request;

class AdministrationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->language->load('Main');        
        $this->language->load('Administration');
    }
    
    public function configuration() {
        
        $data['title'] = $this->language->get('title_configuration') . ' - ' . $this->language->get('title');

        View::renderTemplate('header', $data);
        View::render('Administration/Configuration', $data);
        View::renderTemplate('footer', $data);
    }
    
    public function showAttributeIndex() {
        
        $data['title'] = $this->language->get('title_addattribute') . '-' . $this->language->get('title_configuration') . ' - ' . $this->language->get('title');
        
        $data['attributeList'] = \App\Models\AttributeRepoPdo::getActive();       
        

        View::renderTemplate('header', $data);
        View::render('Administration/Attributes/Index', $data);
        View::renderTemplate('footer', $data);
    }
    
    public function saveAttribute() {
        
        $data = Request::post();
        
        $attributes = [];
        foreach ($data['name'] as $key => $name) {
            
            //ignore empty
            if (empty($name) OR empty($data['values'][$key])) {
                
                !empty($data['id'][$key]) ? \App\Models\AttributeRepoPdo::remove(['id' => $data['id'][$key]]) : ''; 
                
                continue;
            }
            
            $attributes[$key]['id'] = $data['id'][$key];
            $attributes[$key]['name'] = $name;
            $attributes[$key]['values'] = array_map('trim', explode(',', $data['values'][$key]));
        }
        
        \App\Models\AttributeRepoPdo::save($attributes);  
        
        \Helpers\Url::redirect('admin/attributes');     
    }
}
