<?php
namespace Ec2SnapshotsManagement\Commons;

class ResponseStates
{
    const GOOD = 0;
    const ERROR = 1;
    
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const NOT_ALLOWED = 403;
    const NOT_FOUND = 404;
    const MAINTENANCE_MODE = 503;

    const S_UNKNOWN_ERROR = 0;
    const S_TASK_EXIT = 10000;
    const S_GENERAL_ERROR = 10001;
    const S_MISSING_ARGUMENT = 3001;    
    const S_METHOD_NOT_FOUND = 4001;
}

?>
