<?php
define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('APP_DIR', basename(dirname(dirname(__FILE__))));
define('SETTINGS_INI', ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'settings.ini');
define('AWS_CREDENTIALS_INI', ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'credentials.ini');
define('LOG_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR);
require ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
require ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'run.php';
spl_autoload_register('classLoader');
$run = new Run($argv, $argc);
ob_start();
ob_end_flush();

function classLoader($className) 
{
    $class = dirname(dirname(__FILE__)) . str_replace('Ec2SnapshotsManagement', '', str_replace('\\', '/', $className)) . '.php';
    require_once($class);
}

?>