<?php
use Piwik\Ini\IniReader;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Exceptions\ErrorHandler;
use Ec2SnapshotsManagement\Helpers\ConsoleArguments;
use Ec2SnapshotsManagement\Helpers\AwsCredentials;

class Run 
{
    private $iniReader;
    private $argumentValues;
    private $argumentCount;
    private $appSettings;
    private $awsCredentials;
    private $isDebug = false;
    
    public function __construct($argv, $argc) 
    {
        $this->setIniReader();
        $this->setAppSettings();
        $this->setAwsCredentials();
        $this->setDebugMode();
        $this->setDisplayErrors();
        try {
            $task = new ConsoleArguments($argv, $argc);
            $this->runTask($task->getTaskName(), $task->getCommands());
        } catch (Exception $e) {
            ErrorHandler::handle($e, $this->isDebugMode());
        }
        
        // $credentialsProvider = new AwsCredentials();
        // $awsCredentials = $credentialsProvider->setAwsCredentials([
        //     'iniFile' => AWS_CREDENTIALS_INI,
        //     'profile' => 'default'
        // ])->getAwsCredentials();
        // var_dump($awsCredentials);
    }

    private function runTask($taskName, $commands)
    {
        try {
            require ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . $taskName . '.php';
            $task = new RedmineHousekeeping($commands);
            $task->setAppSettings($this->appSettings)
                ->setAwsCredentials($this->awsCredentials)
                ->execute();
            // print_r(get_class($task));
            // print_r($task::getDescription());
            print_r($task::getLogMessages());
            return;
        } catch (Exception $e) {
            ErrorHandler::handle($e, $this->isDebugMode());
        }
    }

    private function setIniReader() 
    {
        $this->iniReader = new IniReader();
    }

    protected function getIniReader() 
    {
        return $this->iniReader;
    }

    private function setAppSettings() 
    {
        $this->appSettings = $this->iniReader->readFile(SETTINGS_INI);
        // $this->setGlobal('APP_SETTINGS', $this->appSettings);
    }

    protected function getAppSettings() 
    {
        return $this->appSettings;
    }

    private function setDebugMode() {
        if (isset($this->getAppSettings()['general']['debug']) && $this->getAppSettings()['general']['debug'] == 1) {            
            $this->isDebug = true;
            // $this->setGlobal('DEBUG_MODE', $this->isDebug);
        }
    }

    private function setAwsCredentials()
    {
        $credentialsProvider = new AwsCredentials();
        $this->awsCredentials = $credentialsProvider->setAwsCredentials([
            'iniFile' => AWS_CREDENTIALS_INI,
            'profile' => $this->getAppSettings()['aws_defaults']['profile']
        ])->getAwsCredentials();
    }

    private function getAwsCredentials()
    {
        return $this->awsCredentials;
    }

    protected function isDebugMode() {
        return $this->isDebug;
    }

    private function setDisplayErrors()
    {
        if (!$this->isDebugMode()) {            
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