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
class Db
{
    public static function placeholders(array $values) {
        
        return implode(',', array_fill(0, count($values), '?'));
    }
    
    public function get() {
        
        return Database::get();
    }
}
