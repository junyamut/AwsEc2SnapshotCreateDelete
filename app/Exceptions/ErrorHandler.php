<?php
namespace Ec2SnapshotsManagement\Exceptions;
use Ec2SnapshotsManagement\Commons\Settings;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

class ErrorHandler
{    
    private static $alertCode;
    private static $message;
    
    public static function handle($exception) 
    {
        self::handleException($exception);        
    }

    public static function handleException($exception)
    {
        return self::renderException($exception);
    }

    public static function setAlertCode($alertCode)
    {
        self::$alertCode = $alertCode;
    }

    public static function setMessage($message) {
        self::$message = $message;
    }

    private static function renderException($exception)
    {
        $additionalInfo = '';
        if (empty(self::$alertCode)) {
            self::$alertCode = ResponseStates::S_UNKNOWN_ERROR;
        }
        if (empty(self::$message)) {
            self::$message = Messages::getMessage(ResponseStates::S_UNKNOWN_ERROR);
        }
        $additionalInfo = ' in ' . $exception->getFile() . ' @ line ' . $exception->getLine();
        $message = '(' . self::$alertCode . ') ' . ($exception->getMessage() . $additionalInfo);
        Settings::getLogger()->warning($message);
    }
}