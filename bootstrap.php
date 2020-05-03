<?php

use QueryBuilder\Model;

require_once 'Model.php';
require_once 'Router.php';


function loader($class)
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $class . '.php';
}

spl_autoload_register('loader');

Model::connectMYSQL([
    'host' => 'localhost',
    'dbname' => 'er_smmpanel'
], 'root', '');
