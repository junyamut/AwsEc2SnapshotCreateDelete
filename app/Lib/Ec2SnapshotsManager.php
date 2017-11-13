<?php
namespace Ec2SnapshotsManagement\Lib;
use Ec2SnapshotsManagement\Commons\Settings;
use Ec2SnapshotsManagement\Commons\Messages;
use GuzzleHttp\Promise;
use Aws\Exception\AwsException;
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
        try {
            $promise = $this->client->createSnapshotAsync([
                'DryRun' => $dryRun,
                'Description' => $description,
                'VolumeId' => $volumeId
            ]);
            $result = $promise->wait();
            return $result;
        } catch (AwsException $e) {
            Settings::getLogger()->error(Messages::formatMultilineMessage(get_class(), $this->getAwsExceptionDetails($e)));
        }
    }

    public function createMultipleSnapshots($snapshots = [], $description = '', $dryRun = true)
    {
        if (!is_array($snapshots) || empty($snapshots)) {
            return false;
        }
        $results = [];
        foreach ($snapshots as $snapshot) {
            $results[] = $this->createSnapshot($snapshot, $description, $dryRun);
        }
        return $results;
    }

    public function deleteSnapshot($snapshotId, $dryRun = true)
    {
        try {
            $promise = $this->client->deleteSnapshotAsync([
                'DryRun' => $dryRun,
                'SnapshotId' => $snapshotId
            ]);        
            $promise->wait();
        } catch (AwsException $e) {
            Settings::getLogger()->error(Messages::formatMultilineMessage(get_class(), $this->getAwsExceptionDetails($e)));
        }
    }

    public function deleteMultipleSnapshots($snapshots = [], $dryRun = true)
    {
        if (!is_array($snapshots) || empty($snapshots)) {
            return false;
        }
        foreach ($snapshots as $snapshot) {
            $this->deleteSnapshot($snapshot, $dryRun);
        }
    }

    public function searchInResult($terms = [], $result)
    {        
        if (is_object($terms)) {
            return false;
        }
        if (is_string($terms)) {
            $terms = explode(',', $terms);
        }
        $found = [];
        foreach($terms as $key => $term) {
            $term = trim($term);
            $found[] = $term . ': ' . $result->search($term);
        }        
        return $found;
    }

    public function searchInMultipleResults($terms = [], $results)
    {
        if (!is_array($results)) {
            return false;
        }
        $found = [];
        foreach ($results as $result) {
            $found[] = $this->searchInResult($terms, $result);
        }
        return $found;
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

    private function getAwsExceptionDetails($awsException) 
    {
        return [
            'HttpStatusCode: ' . $awsException->getStatusCode(),
            'AwsRequestId: ' . $awsException->getAwsRequestId(),
            'AwsErrorCode: ' . $awsException->getAwsErrorCode(),
            'AwsErrorCode: ' . $awsException->getAwsErrorMessage()
        ];
    }
}

?>