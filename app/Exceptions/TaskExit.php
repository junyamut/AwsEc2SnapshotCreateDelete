<?php
namespace Ec2SnapshotsManagement\Exceptions;
use \Exception;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

class TaskExit extends Exception
{
    public function __construct()
    {   
        parent::__construct(Messages::getMessage(ResponseStates::S_TASK_EXIT), ResponseStates::S_TASK_EXIT);
    }
}

?>