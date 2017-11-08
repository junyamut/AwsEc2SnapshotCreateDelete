<?php
namespace Ec2SnapshotsManagement\Helpers;
use Aws\Common\Credentials\Credentials;
use Aws\Credentials\CredentialProvider;

class AwsCredentials
{
    private $awsCredentials = null;
    private $parameters = ['key' => null, 'secret' => null, 'iniFile' => null, 'profile' => null];

    public function __construct($parameters = []) 
    {
        $this->setAwsCredentials($parameters);
    }   

    public function setAwsCredentials($parameters = [])
    {        
        $credentials = $this->evaluate(array_merge($this->parameters, $parameters));
        if ($credentials != false) {
            $this->awsCredentials = $credentials;
        }
        return $this;
    }

    public function getAwsCredentials() 
    {
        return $this->awsCredentials;
    }

    private function evaluate($parameters)
    {
        if (empty($parameters)) {
            return false;
        } else if (empty($parameters['key']) && empty($parameters['secret']) && empty($parameters['iniFile'])) {
            return false;
        } else if (empty($parameters['key']) && empty($parameters['secret']) && !empty($parameters['iniFile'])) {
            return $this->iniCredentialsProvider($parameters['iniFile'], $parameters['profile']);
        } else if (!empty($parameters['key']) && !empty($parameters['secret']) && empty($parameters['iniFile'])) {
            return $this->newCredentialsProvider($parameters['key'], $parameters['secret']);
        }
        return false;
    }

    private function newCredentialsProvider($key, $secret) 
    {
        return new Credentials($key, $secret);
    }

    private function iniCredentialsProvider($iniFile, $profile = 'default')
    {
        $provider = CredentialProvider::ini($profile, $iniFile);
        return CredentialProvider::memoize($provider);
    }
}

?>