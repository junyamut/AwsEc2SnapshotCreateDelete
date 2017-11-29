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

    public static function handleError($errorNum, $errorString, $errorFile, $errorLine)
    {      
        self::handleException(new TaskException($errorString, $errorNum));
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
        if (empty(self::$alertCode)) {
            self::$alertCode = (null !== $exception->getCode()) ? $exception->getCode() : ResponseStates::S_UNKNOWN_ERROR;
        }
        if (empty(self::$message)) {
            self::$message = (null !== $exception->getMessage()) ? $exception->getMessage() : Messages::getMessage(ResponseStates::S_UNKNOWN_ERROR);
        }
        $additionalInfo = ' in ' . $exception->getFile() . ' @ line ' . $exception->getLine();
        $message = '(' . self::$alertCode . ') ' . (self::$message . $additionalInfo);
        Settings::getLogger()->warning($message);
    }
}