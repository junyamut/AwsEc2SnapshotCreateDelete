<?php
namespace Ec2SnapshotsManagement\Helpers;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

class ConsoleArguments 
{
    private $minArgumentCount = 3;
    private $valuesList;
    private $count;

    public function __construct($argv, $argc) 
    {
        $this->valuesList = $argv;
        $this->count = $argc;
        $this->validate();
    }

    public function getTaskName()
    {
        return $this->valuesList[2];
    }

    public function getOptions()
    {        
        $this->options = array_slice($this->valuesList, $this->minArgumentCount);
        return $this;
    }

    public function asArray()
    {
        return $this->options;
    }

    public function asString()
    {
        return implode(' ', $this->options);
    }

    public function getCount() 
    {
        return $this->count;
    }

    private function validate() 
    {
        if ($this->count < $this->minArgumentCount) {
            throw new TaskException(Messages::getMessage(ResponseStates::S_MISSING_ARGUMENT), ResponseStates::S_MISSING_ARGUMENT);
        }
    }
}

?>
