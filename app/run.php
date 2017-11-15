<?php
use Piwik\Ini\IniReader;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\StdoutHandler;
use Ec2SnapshotsManagement\Commons\Settings;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Exceptions\ErrorHandler;
use Ec2SnapshotsManagement\Helpers\ConsoleArguments;
use Ec2SnapshotsManagement\Commons\Messages;
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
            $this->runTask($task->getTaskName(), $task->getOptions());
        } catch (Exception $e) {
            ErrorHandler::setAlertCode($e->getCode());
            ErrorHandler::handle($e);
        }        
    }

    private function runTask($taskName, $options)
    {
        try {
            require ROOT_DIR . DIRECTORY_SEPARATOR . APP_DIR . DIRECTORY_SEPARATOR . 'Tasks' . DIRECTORY_SEPARATOR . $taskName . '.php';
            $task = new RedmineHousekeeping($options);
            $task->printTaskDetails();
            $task->setAwsCredentials($this->awsCredentials)->execute();
        } catch (Exception $e) {
            ErrorHandler::handle($e);
        }
    }

    private function setAppSettings() 
    {        
        Settings::load($this->appSettings);
        Settings::convert();
        //[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n DEFAULT FORMAT
        $streamFormatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context%" . PHP_EOL, 'Y-m-d H:i:s.u');
        $consoleFormatter = new LineFormatter("- %message% %context%" . PHP_EOL);
        $stream = new StreamHandler(LOG_DIR . '/app-' . date('Y-m-d') . '.log', Logger::DEBUG);
        $stream->setFormatter($streamFormatter);
        $console = new StdoutHandler();
        $console->setFormatter($consoleFormatter);
        $logger = new Logger('AWS_EC2_SS_Manager');
        $logger->pushHandler($stream);
        $logger->pushHandler($console);
        Settings::setLogger($logger);
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
}    
?>