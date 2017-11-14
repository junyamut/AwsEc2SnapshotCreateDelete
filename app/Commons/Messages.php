<?php
namespace Ec2SnapshotsManagement\Commons;

class Messages 
{
    public static $missingArgument = 'Missing an argument!'; // 3001
    public static $methodNotFound = 'Method was not found.'; // 4001
    public static $viewLogFileNotice = 'View logs @ ' . ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . '/logs/app-{Y-m-d}.log for more details.'; //  5001
    public static $taskDoneNotice = 'Done with task.'; //  5002
    public static $unknownError = 'Unknown error occurred.'; // NONE
    public static $taskExit = 'Exiting task...'; // 10000
    public static $generalError = 'Sorry, I cannot process this task'; // 10001

    public static function getMessage($messageId) 
    {
        $message = '';

        switch($messageId) {            
            case 3001;
                $message = self::$missingArgument;
                break;
            case 4001;
                $message = self::$methodNotFound;
                break;
            case 5001;
                $message = self::$viewLogFileNotice;
                break;
            case 5002;
                $message = self::$taskDoneNotice;
                break;
            case 10000;
                $message = self::$taskExit;
                break;
            case 10001;
                $message = self::$generalError;
                break;                
            default:
                $message = self::$unknownError;
        }
        return $message;
    }    

    public static function convertToUtf8($text)
    {
        $encoding = mb_detect_encoding($text, mb_detect_order(), false);
        if ($encoding != 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $encoding);
        }        
        return iconv('UTF-8', 'UTF-8//TRANSLIT', $text);
    }

    public static function formatTaskMessage($taskName = null, $message = null)
    {
        return self::convertToUtf8($taskName) . ' :: ' . self::convertToUtf8($message);
    }
}

?>