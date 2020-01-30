<?php
function loader($class)
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $class . '.php';
}

spl_autoload_register('loader');