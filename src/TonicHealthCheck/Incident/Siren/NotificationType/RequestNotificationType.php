<?php

namespace TonicHealthCheck\Incident\Siren\NotificationType;

use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\StreamFactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\Subject\SubjectInterface;

/**
 * Class RequestNotificationType
 * @package TonicHealthCheck\Incident\Siren\NotificationType;
 */
class RequestNotificationType implements NotificationTypeInterface
{
    /**
     * @var HttpMethodsClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $resourceUrl;

    /**
     * RequestNotificationType constructor.
     * @param HttpMethodsClient $httpClient
     * @param string            $resourceUrl
     */
    public function __construct(HttpMethodsClient $httpClient, $resourceUrl)
    {
        $this->setHttpClient($httpClient);
        $this->setResourceUrl($resourceUrl);

    }

    /**
     * @param SubjectInterface  $subject
     * @param IncidentInterface $incident
     */
    public function notify(SubjectInterface $subject, IncidentInterface $incident)
    {
        $serverUrl = $subject;
        if ($incident->getStatus() != IncidentInterface::STATUS_OK) {
            $this->incidentUpdate($incident, $serverUrl);
        } else {
            $this->incidentCreate($incident, $serverUrl);
        }
    }

    /**
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }


    /**
     * @param HttpMethodsClient $httpClient
     */
    protected function setHttpClient(HttpMethodsClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $resourceUrl
     */
    protected function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;
    }

    /**
     * @param IncidentInterface $incident
     * @param $serverUrl
     */
    protected function incidentUpdate(IncidentInterface $incident, $serverUrl)
    {
        $response = $this->getHttpClient()->post(
            $serverUrl.$this->getResourceUrl(),
            ['Content-type' => 'application/json'],
            json_encode(
                [
                    'name' => $incident->getIdent(),
                    'message' => $incident->getMessage(),
                    'status' => 1,
                    "visible" => 1,
                ]
            )
        );
        if ($response) {
            $data = json_decode($response->getBody()->getContents());
            if ($data->data->id) {
                $incident->setExternalId($data->data->id);
            }
        }
    }

    /**
     * @param IncidentInterface $incident
     * @param $serverUrl
     */
    protected function incidentCreate(IncidentInterface $incident, $serverUrl)
    {
        $this->getHttpClient()->put(
            $serverUrl.$this->getResourceUrl().'/'.$incident->getExternalId(),
            ['Content-type' => 'application/json'],
            json_encode(
                [
                    'status' => 4,
                ]
            )
        );
    }
}