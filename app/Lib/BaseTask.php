<?php
namespace Ec2SnapshotsManagement\Lib;
use Ec2SnapshotsManagement\Commons\Settings;
use Ec2SnapshotsManagement\Exceptions\TaskException;
use Ec2SnapshotsManagement\Exceptions\ErrorHandler;
use Ec2SnapshotsManagement\Interfaces\TaskTemplate;
use Ec2SnapshotsManagement\Commons\TimeConversion;
use Ec2SnapshotsManagement\Commons\Messages;
use Ec2SnapshotsManagement\Commons\ResponseStates;
use Ec2SnapshotsManagement\Lib\Ec2SnapshotsManager;

abstract class BaseTask implements TaskTemplate
{    
    public static $_CONF            = [
                                        'command', 
                                        'delete_older_than',  
                                        'is_dry_run',
                                        'region' ,
                                        'retain_min',  
                                        'version',
                                        'volumes', 
                                        'task_name', 
                                        'task_description'
                                    ];
    protected                 
        $awsCredentials,
        $command,
        $ec2SnapshotManager,
        $isDryRun,
        $forDeletion,
        $logMessages,
        $snapshotsList;
    protected static        
        $M_COUNT_BELOW_THRESHOLD    = 'Total snapshots count is within retention threshold. Will not attempt to delete any.',
        $M_EMPTY_LIST               = 'Snapshots list is empty. Nothing to delete.',
        $M_LISTED_FOR_CREATION      = ' - this volume was scheduled for a snapshot.',
        $M_LISTED_FOR_DELETION      = ' - this snapshot was scheduled for deletion.',
        $M_NONE_FOR_DELETION        = 'No snapshots were scheduled for deletion.';
        

    public function __construct($conf = []) 
    {
        self::$_CONF = array_merge([
            'command' => '',
            'delete_older_than' => Settings::getConfig()->rules->delete_older_than,
            'is_dry_run' => Settings::getConfig()->aws_defaults->dry_run,
            'region' => Settings::getConfig()->aws_defaults->region,
            'retain_min' => Settings::getConfig()->rules->retain_min,
            'version' => Settings::getConfig()->aws_defaults->version,
            'volumes' => [],
            'task_name' => 'Task Name',
            'task_description' => 'Task Description'
        ], $conf);
    }

    public function getName()
    {
        return self::$_CONF['task_name'];
    }

    public function getDescription()
    {
        return self::$_CONF['task_description'];
    }

    public function getLogMessages()
    {
        return $this->logMessages;
    }

    public function setAwsCredentials($awsCredentials)
    {
        $this->awsCredentials = $awsCredentials;
        return $this;
    }

    private function setEc2SnapshotManager()
    {
        $this->ec2SnapshotManager = new Ec2SnapshotsManager([            
            'credentials' => $this->awsCredentials,
            'region' => self::$_CONF['region'],
            'version' => self::$_CONF['version'],
            'volumes' => self::$_CONF['volumes']
        ]);
    }

    public function setConf($conf = []) {
        self::$_CONF = array_merge(self::$_CONF, $conf);
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
            
    public function execute()
    {
        $this->setEc2SnapshotManager();
        $this->getSnapshotsList();
        try {
            $this->callbackMethod(self::$_CONF['command'], []);
        } catch (TaskException $e) {
            ErrorHandler::setAlertCode($e->getCode());
            ErrorHandler::handle($e);
        }
        return;
    }

    protected function getSnapshotsList()
    {        
        $this->snapshotsList = $this->ec2SnapshotManager
            ->Ec2ClientConnect()
            ->filters()
            ->getSnapshotsList()['Snapshots'];
    }

    protected function totalSnapshots()
    {
        return count($this->snapshotsList); // Total snapshots found associated to specified volume(s)
    }

    protected function enumerate()
    {        
        Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, 'No. of snapshots found = ' . $this->totalSnapshots()));
        foreach ($this->snapshotsList as $index => $list) {
            Settings::getLogger()->info(('#' . ($index + 1)), [
                'VolumeId: ' . $list['VolumeId'],
                'SnapshotId: ' . $list['SnapshotId'],
                'State: ' . $list['State'],
                'StartTime: ' . $list['StartTime']->format('Y-m-d H:i:s')
            ]);
        }
    }

    protected function create()
    {
        $forCreation = [];
        foreach (self::$_CONF['volumes'] as $volumeId) {
            Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, ($volumeId . self::$M_LISTED_FOR_CREATION)));
            $forCreation[] = $volumeId;
        }        
        $results = $this->ec2SnapshotManager->Ec2ClientConnect()->createMultipleSnapshots($forCreation, self::$_CONF['task_description'], self::$_CONF['is_dry_run']);
        if (!self::$_CONF['is_dry_run']) {
            $search = $this->ec2SnapshotManager->searchInMultipleResults('VolumeId, SnapshotId, State, StartTime', $results);        
            foreach ($search as $found) {
                Settings::getLogger()->info(('#' . ($index + 1)), $found);
            }
        }
    }

    protected function delete()
    {
        $this->beforeDelete();
        foreach ($this->snapshotsList as $snapshot) {
            $age = TimeConversion::timeInterval($snapshot['StartTime'], 'days', 'Asia/Singapore');
            if (abs($age) >= self::$_CONF['delete_older_than']) {
                Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, ($snapshot['SnapshotId'] . self::$M_LISTED_FOR_DELETION)));
                $this->forDeletion[] = $snapshot['SnapshotId'];
            }
        }
        $this->ec2SnapshotManager->Ec2ClientConnect()->deleteMultipleSnapshots($this->forDeletion, self::$_CONF['is_dry_run']); // result is empty when successful
        $this->afterDelete();
    }

    protected function beforeDelete()
    {
        if ($this->totalSnapshots() == 0) {
            Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, self::$M_EMPTY_LIST));
            throw new TaskException(Messages::getMessage(ResponseStates::S_TASK_EXIT), ResponseStates::S_TASK_EXIT);
        }
        if ($this->totalSnapshots() <= self::$_CONF['retain_min']) {
            Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, self::$M_COUNT_BELOW_THRESHOLD));
            throw new TaskException(Messages::getMessage(ResponseStates::S_TASK_EXIT), ResponseStates::S_TASK_EXIT);
        }
        return;
    }

    protected function afterDelete()
    {
        if (empty($this->forDeletion)) {
            Settings::getLogger()->info(Messages::formatTaskMessage(__METHOD__, self::$M_NONE_FOR_DELETION));
        }
        return;
    }    

    public function printTaskDetails()
    {
        print 'Welcome to ' . Settings::getConfig()->general->app_name . '!' . PHP_EOL;
        print 'Task: ' . self::$_CONF['task_name'] . PHP_EOL;
        print 'Description: ' . self::$_CONF['task_description'] . PHP_EOL;
        print Messages::getMessage(ResponseStates::S_VIEW_LOGS_NOTICE) . PHP_EOL;
        print PHP_EOL;
        return $this;
    }    
}

?>