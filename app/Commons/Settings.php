<?php
namespace Ec2SnapshotsManagement\Commons;
use \stdClass;

abstract class Settings
{    
    private static $config;
    private static $appSettings;

    public static function load($appSettings = [])
    {
        self::$appSettings = $appSettings;
    }

    public static function getConfig()
    {
        return self::$config;
    }

    public static function convert()
    {        
        $object = new stdClass();
        self::$config = self::arrayToObject(self::$appSettings, $object);
    }

    private static function arrayToObject($array, &$object)
    {        
        foreach ($array as $key => $value)  {
            if (is_array($value)) {
                $object->$key = new stdClass();
                self::arrayToObject($value, $object->$key);
            } else {
                $object->$key = $value;
            }
        }        
        return $object;
    }
}

?>