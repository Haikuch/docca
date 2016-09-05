<?php

    define('buh', '<div class="phperror">BUH</div>');

    function myerror_handler($level, $message, $file, $line, $context) {    
    
        $file = substr(strstr($file, 'ruvik2'), 6);
        $message = explode(":",$message);

        require('errortpl.tpl.php');            
    }

    function mydump($var, $exit=false) {

         echo "<div style='background:white;'>*** DEBUGGING VAR ***<pre>";

        if (is_array($var) || is_object($var)) {
            echo htmlentities(print_r($var, true));
        } elseif (is_string($var)) {
            echo "string(" . strlen($var) . ") \"" . htmlentities($var) . "\"\n";
        } else {
            var_dump($var);
        }

        echo "\n</pre></div>";

        if ($exit) {
            exit;
        }

    }      

    function err($var = 'BUH', $deep = 0) {
        
        $backtrace = debug_backtrace();
        
        for ($i=0;$i<=$deep;$i++) {
        
            $file[$i] = substr($backtrace[$i]['file'], strripos($backtrace[$i]['file'], '/')+1);        
            $line[$i] = $backtrace[$i]['line'];
            $function[$i] = $backtrace[$i+1]['function'];
        }
        
        require('myerror.tpl.php');
    }
    
    function erraj($var = 'BUH', $file="", $line="", $before="", $after="") {

        $file = substr(strstr($file, 'ruvik2'), 6);
        
        $backtrace = debug_backtrace();
        
        $file = substr($backtrace[0]['file'], strripos($backtrace[0]['file'], '/')+1);
        $line = $backtrace[0]['line'];
        
        echo $before;
        
        echo $file.' - '.$line.': |';
        (is_object($var)  OR is_array($var)) ? var_dump( $var) : print_r(htmlspecialchars($var));
        echo '|';
    }
    
    function errjs($var, $file="", $line="") {        
        
         err($var, $file, $line, "</script>","<script>");
    }
    
    function stopjs() {
        
        echo '</script>';
    }
    
    //set_error_handler('myerror_handler');
        
?>
