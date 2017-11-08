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

    public function getCommands()
    {
        return array_slice($this->getArgumentsAsArray(), 3);
    }

    public function getArgumentsAsArray() 
    {
        return $this->valuesList;
    }

    public function getArgumentsAsString() 
    {
        return implode(',', $this->valuesList);
    }

    public function getCount() 
    {
        return $this->count;
    }

    private function validate() 
    {
        if ($this->count < $this->minArgumentCount) {
            throw new TaskException(Messages::getMessage(ResponseStates::S_MISSING_ARGUMENT));
        }
    }
}

?>
