<?php
/**
 * Data Helper
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

namespace Helpers;

/**
 * Common data lookup methods.
 */
class Verify
{
    //
    public static function mkTimestamp($value) {
        
        //is timestamp
        if (is_int($value)) {
            
            return $value;
        }
        
        //dd.mm.YYYY
        if (preg_match('%^(0?[1-9]|[12][0-9]|3[01])[- /.](0?[1-9]|1[012])[- /.](0-20)?[\d]{2}$%' , $value)) {
        
            $date = explode('.', $value);
            return mktime(0,0,0, $date[1], $date[0], $date[2]);
        }
        
        return NULL;
    }
}
