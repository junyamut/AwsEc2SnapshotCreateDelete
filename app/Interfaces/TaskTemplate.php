<?php
namespace Ec2SnapshotsManagement\Interfaces;

interface TaskTemplate
{    
    public function getLogMessages();
    public function getName();
    public function getDescription();
    public function execute();
    public function initConsole();
    public function setAwsCredentials($awsCredentials);
    public function callbackMethod($methodName, $args);
    public function printTaskDetails();
}

?>