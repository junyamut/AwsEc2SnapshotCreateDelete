<?php
namespace Ec2SnapshotsManagement\Lib;
use Aws\Ec2\Ec2Client;

class Ec2SnapshotsManager
{
    protected $client;    
    protected $volumes;
    protected $credentials;
    protected $region;
    protected $version;
    protected $filters;
    private $parameters = ['volumes' => [], 'credentials' => null, 'region' => null, 'version' => 'latest'];

    public function __construct($parameters = [])
    {
        if ($this->evaluate(array_merge($this->parameters, $parameters))) {
            $this->volumes = $parameters['volumes'];
            $this->credentials = $parameters['credentials'];
            $this->region = $parameters['region'];
            $this->version = $parameters['version'];
        }
    }

    private function getVolumes()
    {
        return $this->volumes;
    }

    private function getCredentials()
    {
        return $this->credentials;
    }

    private function getRegion()
    {
        return $this->region;
    }

    private function getVersion()
    {
        return $this->version;
    }

    public function Ec2ClientConnect() 
    {
        $this->client = Ec2Client::factory([
            'credentials' => $this->getCredentials(),
            'region' => $this->getRegion(),
            'version' => $this->getVersion()
        ]);
        return $this;
    }

    public function getSnapshotsList()
    {
        return $this->client->describeSnapshots($this->filters);
    }

    public function createSnapshot($volumeId, $description = '', $dryRun =  true)
    {
        return $this->client->createSnapshot([
            'DryRun' => $dryRun,
            'Description' => $description,
            'VolumeId' => $volumeId
        ]);
    }

    public function deleteSnapshot($snapshotId, $dryRun = true)
    {
        return $this->client->deleteSnapshot([
            'DryRun' => $dryRun,
            'SnapshotId' => $snapshotId
        ]);
    }

    public function deleteMultipleSnapshots($snapshots = [], $dryRun = true)
    {
        if (!is_array($snapshots) || empty($snapshots)) {
            return false;
        }

        $results = [];
        foreach ($snapshots as $snapshot) {
            $results[] = $this->deleteSnapshot($snapshot, $dryRun);
        }

        return $results;
    }

    public function filters($filters = []) 
    {
        if (empty($filters)) {
            $this->filters = [
                'DryRun' => false,
                'Filters' =>[
                    [
                        'Name' => 'volume-id',
                        'Values' => $this->getVolumes()
                    ]
                ]
            ];
        } else {
            $this->filters = $filters;
        }
        return $this;
    }

    private function evaluate($parameters)
    {
        if (empty($parameters)) {
            return false;
        } else if (empty($parameters['volumes']) && empty($parameters['credentials']) && empty($parameters['region']) && empty($parameters['version'])) {
            return false;
        } else if (!empty($parameters['volumes']) && !empty($parameters['credentials']) && !empty($parameters['region']) && !empty($parameters['version'])) {
            return true;
        }
        return false;
    }
}

?>