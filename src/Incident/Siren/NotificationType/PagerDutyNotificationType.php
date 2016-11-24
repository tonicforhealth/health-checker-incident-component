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
        $event = new Event();
        $event->serviceKey = $this->getServiceKey();
        $event->incidentKey = sprintf('hci_%d', $incident->getId());
        if ($incident->getStatus() != IncidentInterface::STATUS_OK) {
            $event->description = $incident->getIdent();
            $event->eventType = Event::EVENT_TYPE_TRIGGER;
            $event->details = [
                'log' => $incident->getMessage(),
                'status' => $incident->getStatus(),
                'type' => $incident->getType(),
                'id' => $incident->getId(),
            ];
        } else {
            $event->eventType = Event::EVENT_TYPE_RESOLVE;
        }
        $this->getEventClient()->post($event);
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
