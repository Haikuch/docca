<?php
/**
 * database Helper
 *
 * @author David Carr - dave@novaframework.com
 * @version 3.0
 */

namespace Helpers;

use PDO;

/**
 * Extending PDO to use custom methods.
 */
class Database extends DatabaseOrig {
    
    /**
     * Delete method
     *
     * @param  string $table table name
     * @param  array $where array of columns and values
     * @param  integer   $limit limit number of records
     */
    public function delete($table, $where, $limit = false)
    {
        return parent::delete($table, $where, $limit);
    }

}
