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
        $options = implode(' ', array_slice($this->getArgumentsAsArray(), $this->minArgumentCount));
        if (preg_match('/^--([a-z0-9-]{1,})\s([a-z0-9]{1,})|^--([a-z0-9]{1,})/i', $options, $match)) {
            unset($match[0]);
            return array_values(array_filter($match));
        }
        return false;
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
            throw new TaskException(Messages::getMessage(ResponseStates::S_MISSING_ARGUMENT), ResponseStates::S_MISSING_ARGUMENT);
        }
    }
}

?>
