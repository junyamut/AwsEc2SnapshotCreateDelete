<?php
namespace Ec2SnapshotsManagement\Commons;

class Messages 
{
    public static $missingArgument = 'Missing argument!'; // 3001
    public static $unknownError = 'Unknown error occurred.'; // NONE
    public static $generalError = 'Sorry, I cannot process this task'; // 10000

    public static function getMessage($messageId) 
    {
        $message = '';

        switch($messageId) {            
            case 3001;
                $message = self::$missingArgument;
                break;
            case 10000;
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
}

?>