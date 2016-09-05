<?php
/**
 * String Helper
 *
 */

namespace Helpers;

/**
 * Common data lookup methods.
 */
class String
{
    static public function cutSeperator( $string, $seperator ) {
        
        if ( strlen( $seperator ) > 0 ) {

            return substr( $string, 0, -strlen( $seperator ) );
        } 
        
        else {
            
            return $string;
        }
    }
}
