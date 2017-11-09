<?php
namespace Ec2SnapshotsManagement\Lib;
use Ec2SnapshotsManagement\Interfaces\TaskTemplate;

abstract class BaseTask implements TaskTemplate
{
    protected $taskName;
    protected $taskDescription;
    protected $logMessages;
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

    }

    public function setAwsCredentials($awsCredentials)
    {

    }
}

?>