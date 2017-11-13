<?php
namespace Ec2SnapshotsManagement\Lib;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Interfaces\TaskTemplate;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

abstract class BaseTask implements TaskTemplate
{
    protected $taskName;
    protected $taskDescription;
    protected $logMessages;
    protected $options;
    protected $awsCredentials;

    public function getName()
    {
        return $this->taskName;
    }

    public function getDescription()
    {
        return $this->taskDescription;
    }

    public function getLogMessages()
    {
        return $this->logMessages;
    }
            
    public function execute()
    {
        return $this;
    }

    public function setAwsCredentials($awsCredentials)
    {
        return $this;
    }

    public function callbackMethod($methodName, $parameters = null)
    {
        if (empty($methodName)) {
            return;
        }
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($parameters);
        } else {
            $message = Messages::formatTaskMessage($this->getName(), 'Call to method {' . $methodName . '} - ' . Messages::getMessage(ResponseStates::S_METHOD_NOT_FOUND));
            throw new TaskException($message, ResponseStates::S_METHOD_NOT_FOUND);
        }
    }

    public function printTaskDetails()
    {
        print 'Task: ' . $this->getName() . PHP_EOL;
        print 'Description: ' . $this->getDescription() . PHP_EOL;
        print Messages::getMessage(ResponseStates::S_VIEW_LOGS_NOTICE) . PHP_EOL;
    }
}

?>