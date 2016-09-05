<?php

require 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

function getDoctrine() {
    
    $paths = array("/path/to/entity-files");
    $isDevMode = false;

    // the connection configuration
    $dbParams = array(
        'driver'   => 'pdo_mysql',
        'user'     => 'root',
        'password' => 'oralb',
        'dbname'   => 'docca',
    );

    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
    
    return EntityManager::create($dbParams, $config);
}
