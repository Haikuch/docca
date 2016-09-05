<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Helpers;

/**
 * Description of DateTime
 *
 * @author calvin
 */
class DateTime extends \DateTime {
    
    public function __construct( $time = "now" , DateTimeZone $timezone = NULL ) {
        
        //int is read as timestamp
        if (is_int($time)) {
            
            parent::__construct('now', $timezone);
            
            $this->setTimestamp($time);
        }
        
        else {
        
            parent::__construct($time, $timezone);
        }
    }
}
