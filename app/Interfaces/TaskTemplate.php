<?php
namespace Ec2SnapshotsManagement\Interfaces;

interface TaskTemplate
{
    public static function getName();
    public static function getDescription();
    public static function getLogMessages();
    public function execute();    
    public function setAwsCredentials($awsCredentials);
}

?>