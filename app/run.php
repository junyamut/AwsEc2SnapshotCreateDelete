<?php
use Piwik\Ini\IniReader;
use Psr\Log\LogLevel;
use Katzgrau\KLogger\Logger;
use Ec2SnapshotsManagement\Commons\Settings;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Exceptions\ErrorHandler;
use Ec2SnapshotsManagement\Helpers\ConsoleArguments;
use Ec2SnapshotsManagement\Helpers\AwsCredentials;

class Run 
{
    private $argumentValues;
    private $argumentCount;
    private $appSettings;
    private $awsCredentials;
    
    public function __construct($argv, $argc) 
    {
        $iniReader = new IniReader();
        $this->appSettings = $iniReader->readFile(SETTINGS_INI);
        $this->setDebugMode();
        $this->setAppSettings();        
        $this->setAwsCredentials();        
        try {
            $task = new ConsoleArguments($argv, $argc);
            $this->runTask($task->getTaskName(), $task->getCommands());
        } catch (Exception $e) {
            ErrorHandler::handle($e);
        }        
    }

    private function runTask($taskName, $commands)
    {
        try {
            require ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . $taskName . '.php';
            $task = new RedmineHousekeeping($commands);
            $task->setAwsCredentials($this->awsCredentials)
                ->execute();
            // print_r($task->getName());
            // print_r($task->getDescription());
            print_r($task->getLogMessages());
            return;
        } catch (Exception $e) {
            ErrorHandler::handle($e);
        }
    }

    private function setAppSettings() 
    {        
        Settings::load($this->appSettings);
        Settings::convert();
        Settings::setLogger(new Logger(LOG_DIR, LogLevel::DEBUG, ['extension' => 'log']));
    }

    private function setAwsCredentials()
    {
        $credentialsProvider = new AwsCredentials();
        $this->awsCredentials = $credentialsProvider->setAwsCredentials([
            'iniFile' => AWS_CREDENTIALS_INI,
            'profile' => $this->appSettings['aws_defaults']['profile']
        ])->getAwsCredentials();
    }

    private function setDebugMode() 
    {
        $isDebug = false;
        if (isset($this->appSettings['general']['debug']) && $this->appSettings['general']['debug'] == 1) {            
            $isDebug = true;
        }

        if (!$isDebug) {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            ini_set('error_reporting', 0);
        } else {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('error_reporting', E_ALL);
        }
    }

    private function setGlobal($key, $value)
    {
        global ${$key};
        ${$key} = $value;
    }
}    
?>