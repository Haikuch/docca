<?php
namespace App\Controllers;

use Core\View;
use Core\Controller;
use \Helpers\Request;
use \Helpers\Arr;

use \App\Models;
        
class DocumentController extends Controller
{
    public function __construct() {
        
        parent::__construct();
        
        $this->language->load('Main');
        $this->language->load('Document');
    }
    
    public function index() {
        
        $data['title'] = $this->language->get('title_index') . ' - ' . $this->language->get('title');
        
        $data['documents'] = Models\DocumentRepo::getAllActive(); #err($data['docs']);
        
        View::renderTemplate('header', $data);
        View::render('Document/Index', $data);
        View::renderTemplate('footer', $data);
    }
    
    //
    public function formSearch() {
        
        //
        if (Request::post('filters')) { #err(Request::post('sourcetime')); die();
            
            $params = '?';
            foreach (Request::post('filters') as $filterName) {
                
                $value = Request::post($filterName);
                
                //todo: dirty hack, filters must get methods for valuefields
                if ($filterName == 'sourcetime') {
                    
                    $value = implode('-', $value);
                } 
                
                $params .= 'filter['.$filterName.']=' . $value . '&';
            }
            
            $params = \Helpers\String::cutSeperator($params, '&');
        }
        
        \Helpers\Url::redirect('docs/find' . $params);
    }
    
    public function searchResult() {
               
        $filterList = new Models\FilterList(Request::get('filter'));
        $data['marked'] = $filterList->getMarked();
        
        //find documents
        $data['documents'] = Models\DocumentRepo::getFiltered($filterList);
        
        //--VIEW--
        $data['title'] = $this->language->get('title_index') . ' - ' . $this->language->get('title');
        
        View::renderTemplate('header', $data);
        View::render('Document/Filterbar', ['filterList' => $filterList]);
        
        $noResult = empty($data['documents']) ? '_noresult' : '';        
        View::render('Document/Index' . $noResult, $data);
        
        View::renderTemplate('footer', $data);
    }

    public function add() { 
        
        $data['title'] = $this->language->get('title_add') . ' - ' . $this->language->get('title');

        $data['document'] = Models\DocumentRepo::getEmpty();
        
        View::renderTemplate('header', $data);
        View::render('Document/Add', $data);
        View::renderTemplate('footer', $data);
    }
    
    public function view($docId) {
        
        $data['document'] = Models\DocumentRepo::getOneById($docId);
        
        $data['title'] = $data['document']->getName() . ' - ' . $this->language->get('title');
        
        View::renderTemplate('header', $data);
        View::render('Document/View', $data);
        View::renderTemplate('footer', $data);
        
    }
    
    public function edit($docId) {
        
        $data['document'] = Models\DocumentRepo::getOneById($docId);
        
        $data['title'] = $data['document']->getName() . ' - ' . $this->language->get('title');
        
        View::renderTemplate('header', $data);
        View::render('Document/Edit', $data);
        View::renderTemplate('footer', $data);
    }

    public function save() { 
        
        $request = Request::post();
        $request['newfiles'] = $_FILES['newfiles'];
        $request = $this->reformatRequest($request);
        $request['active'] = 1;
        
        //remove button is pressed
        isset($request['remove']) ? \Helpers\Url::redirect('docs/remove/' . $request['id']) : '';
        
        //save Document
        Models\DocumentRepo::unlinkFileByFileId($request['fileIdsToDelete']);
        $docId = Models\DocumentRepo::save(Models\DocumentFactory::make($request));
        
        //jump to view
        \Helpers\Url::redirect('docs/view/' . $docId);
    }
    
    public function reformatRequest($request) {
        
        //replace empty name with first filename
        if (empty($request['name']) AND !empty($request['newfiles']['name'][0])) {
            
            $request['name'] = $request['newfiles']['name'][0];
        }
        
        //parsing tags
        $tagNames = explode(',', $request['tags']);
        $request['tags'] = [];
        foreach ($tagNames as $tagName) {
            
            if (empty($tagName)) {
                
                continue;
            }
            
            $request['tags'][]['name'] = trim($tagName);
        }
        
        //parsing files
        $newfiles = $request['newfiles'];
        $request['files'] = [];
        foreach ($newfiles['tmp_name'] as $key => $tmpFile) {
            
            //no empty filenames
            if ($tmpFile == '') {
                
                continue;
            }
            
            //build beautiful array
            $request['files'][$key]['tmpName'] = $tmpFile;
            $request['files'][$key]['name'] = $newfiles['name'][$key];
            $request['files'][$key]['type'] = $newfiles['type'][$key];
            $request['files'][$key]['error'] = $newfiles['error'][$key];
            $request['files'][$key]['size'] = $newfiles['size'][$key];           
        }
        
        //parsing attributes
        $i = 0;
        $attributes = [];
        foreach ($request['attributes'] as $attributeId => $valueId) { 
            
            //
            if (empty($valueId)) {
                
                continue;
            }
            
            $i++;
            
            $attributes[$i]['id'] = $attributeId;
            $attributes[$i]['valueId'] = $valueId;
        }
        $request['attributes'] = $attributes;
        
        //todo: request sollte garnicht als schreibvariable verwendet werden hier. vielleicht einfach nur $data
        return $request;
    }
    
    public function remove($docId) {
        
        Models\DocumentRepo::remove($docId);
        
        \Helpers\Url::redirect('docs');
    }
    
    
}
