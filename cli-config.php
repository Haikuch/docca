<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\EntityManager;

// replace with file to your own project bootstrap
require_once 'bootstrap.php';

return ConsoleRunner::createHelperSet(getDoctrine());