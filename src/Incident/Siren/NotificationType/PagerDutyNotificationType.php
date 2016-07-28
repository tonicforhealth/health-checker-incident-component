<?php

namespace TonicHealthCheck\Incident\Siren\NotificationType;

use TonicForHealth\PagerDutyClient\Client\EventClient;
use TonicForHealth\PagerDutyClient\Entity\Event\Event;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\Subject\SubjectInterface;

/**
 * Class PagerDutyNotificationType.
 */
class PagerDutyNotificationType implements NotificationTypeInterface
{
    /**
     * PagerDuty Event Api client.
     *
     * @var EventClient
     */
    protected $eventClient;

    /**
     * @var string;
     */
    public $serviceKey;

    /**
     * RequestNotificationType constructor.
     *
     * @param EventClient $eventClient
     * @param string      $serviceKey
     */
    public function __construct(EventClient $eventClient, $serviceKey)
    {
        $this->setEventClient($eventClient);
        $this->setServiceKey($serviceKey);
    }

    /**
     * @param SubjectInterface  $subject
     * @param IncidentInterface $incident
     */
    public function notify(SubjectInterface $subject, IncidentInterface $incident)
    {
        if ($incident->getStatus() != IncidentInterface::STATUS_OK) {
            $event = new Event();
            $event->serviceKey = $this->getServiceKey();
            $event->description = $incident->getMessage();
            $this->getEventClient()->post($event);
        }
    }

    /**
     * @return EventClient
     */
    public function getEventClient()
    {
        return $this->eventClient;
    }

    /**
     * @return string
     */
    public function getServiceKey()
    {
        return $this->serviceKey;
    }

    /**
     * @param EventClient $eventClient
     */
    protected function setEventClient($eventClient)
    {
        $this->eventClient = $eventClient;
    }

    /**
     * @param string $serviceKey
     */
    protected function setServiceKey($serviceKey)
    {
        $this->serviceKey = $serviceKey;
    }
}
