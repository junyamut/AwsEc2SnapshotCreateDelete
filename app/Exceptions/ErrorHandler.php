<?php
namespace Ec2SnapshotsManagement\Exceptions;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

class ErrorHandler
{
    private static $statusCode;
    private static $alertCode;
    private static $message;
    private static $isDebug;
    
    public static function handle($exception, $isDebug = false) 
    {        
        self::$isDebug = $isDebug;
        self::handleException($exception);        
    }

    public static function handleException($exception)
    {
        return self::renderException($exception);
    }

    public static function setStatusCode($statusCode)
    {
        self::$statusCode = $statusCode;
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
        if (empty(self::$statusCode)) {
            self::$statusCode = ResponseStates::ERROR;
        }
        if (empty(self::$alertCode)) {
            self::$alertCode = ResponseStates::S_UNKNOWN_ERROR;
        }
        if (empty(self::$message)) {
            self::$message = Messages::getMessage(ResponseStates::S_UNKNOWN_ERROR);
        }

        // if (self::$isDebug == true) {
            // $additionalInfo = ' in ' . $exception->getFile() . ' @ line ' . $exception->getLine();        
        // }
        // switch ($exception->getCode()) {
        //     case 404:
        //         $message = $exception->getCode() . ' :: Not found';
        //         break;
        //     default:                                
        //         $tmp = json_decode($exception->getMessage());
        //         if (json_last_error() == JSON_ERROR_NONE) {
        //             !empty($additionalInfo) ? $tmp->{'additionalInfo'} = $additionalInfo : '';
        //             $message = $tmp;
        //         } else {
        //             $message = $exception->getMessage() . $additionalInfo;
        //         }
        //         break;
        // }
        // http_response_code($exception->getCode());
        // header('Content-Type: application/json');
        // $response = json_encode(array(
        //         'status' => self::$statusCode,
        //         'alertcode' => self::$alertCode,
        //         'message' => $message
        //     )
        // );
        $additionalInfo = ' in ' . $exception->getFile() . ' @ line ' . $exception->getLine();
        $message = date('Y-m-d H:i:s') . ' :: Status: ' . self::$statusCode . ' AlertCode: ' . self::$alertCode . ' Message: ' . ($exception->getMessage() . $additionalInfo) . PHP_EOL;
        $output = fopen('php://output', 'r+');
        fputs($output, $message);
        if (LOG_ERRORS) {
            createLogFile();
            error_log(strip_tags($message), 3, LOG_FILE);
        }
    }
}