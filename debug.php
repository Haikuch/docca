<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    //Debug-Modus aktivieren wenn ordner _debug vorhanden
    if (file_exists(__DIR__ . '/_debug')) {  
    
        define('_DEBUG', true);      
        require_once(__DIR__ . '/_debug/defines.php');        
    }
    
    //Falls kein Debug
    else {
        
        define('_DEBUG', false);
        function err() {}
    }
    
?>
