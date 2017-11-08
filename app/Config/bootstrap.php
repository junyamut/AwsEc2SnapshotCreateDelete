<?php
define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
define('ROOT_DIR', dirname(dirname(dirname(__FILE__))));
define('APP_DIR', basename(dirname(dirname(__FILE__))));
define('SETTINGS_INI', ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'settings.ini');
define('AWS_CREDENTIALS_INI', ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'credentials.ini');
define('LOG_ERRORS', true);
define('LOG_FILE', ROOT_DIR . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR . 'errors.log');
require ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
require ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'run.php';
register_shutdown_function('shutdown');
spl_autoload_register('classLoader');
$run = new Run($argv, $argc);
ob_start();
ob_end_flush();

function classLoader($className) 
{
    $class = dirname(dirname(__FILE__)) . str_replace('Ec2SnapshotsManagement', '', str_replace('\\', '/', $className)) . '.php';
    require_once($class);
}

function shutdown()
{
    $error = error_get_last();
    if ($error && ($error['type'] & E_FATAL)) {
        handleError($error['type'], $error['message'], $error['file'], $error['line']);
    }    
}

function handleError($errno, $errstr, $errfile, $errline) 
{        
    switch ($errno){
        case E_ERROR: // 1 //
            $typestr = 'E_ERROR'; break;
        case E_WARNING: // 2 //
            $typestr = 'E_WARNING'; break;
        case E_PARSE: // 4 //
            $typestr = 'E_PARSE'; break;
        case E_NOTICE: // 8 //
            $typestr = 'E_NOTICE'; break;
        case E_CORE_ERROR: // 16 //
            $typestr = 'E_CORE_ERROR'; break;
        case E_CORE_WARNING: // 32 //
            $typestr = 'E_CORE_WARNING'; break;
        case E_COMPILE_ERROR: // 64 //
            $typestr = 'E_COMPILE_ERROR'; break;
        case E_CORE_WARNING: // 128 //
            $typestr = 'E_COMPILE_WARNING'; break;
        case E_USER_ERROR: // 256 //
            $typestr = 'E_USER_ERROR'; break;
        case E_USER_WARNING: // 512 //
            $typestr = 'E_USER_WARNING'; break;
        case E_USER_NOTICE: // 1024 //
            $typestr = 'E_USER_NOTICE'; break;
        case E_STRICT: // 2048 //
            $typestr = 'E_STRICT'; break;
        case E_RECOVERABLE_ERROR: // 4096 //
            $typestr = 'E_RECOVERABLE_ERROR'; break;
        case E_DEPRECATED: // 8192 //
            $typestr = 'E_DEPRECATED'; break;
        case E_USER_DEPRECATED: // 16384 //
            $typestr = 'E_USER_DEPRECATED'; break;
    }
    $message = date('Y-m-d H:i:s') . ' :: Status: ' . $typestr . ', Message: ' . $errstr . ' in ' . $errfile . ' @ line ' . $errline . PHP_EOL;    
    $output = fopen('php://output', 'r+');
    fputs($output, $message);
    if (LOG_ERRORS) {
        createLogFile();
        error_log(strip_tags($message), 3, LOG_FILE);
    }
}

function createLogFile() 
{
    $dir = dirname(LOG_FILE);
    if (mkdir($dir, 0770)) {
        file_put_contents(LOG_FILE, $message);
    }
}
?>