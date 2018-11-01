<?php
namespace Ec2SnapshotsManagement\Exceptions;
use \Exception;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;

class TaskException extends Exception
{
    public function __construct($message = null, $code = null)
    {
        if (empty($message)) {
            $message = Messages::getMessage(ResponseStates::S_UNKNOWN_ERROR);
        }
        if (empty($code)) {
            $code = ResponseStates::S_UNKNOWN_ERROR;
        }        
        parent::__construct($message, $code);
    }
}

?>