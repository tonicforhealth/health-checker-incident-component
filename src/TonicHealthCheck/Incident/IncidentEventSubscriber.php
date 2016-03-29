<?php

namespace TonicHealthCheck\Incident;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

use TonicHealthCheck\Incident\Siren\IncidentSiren;
use TonicHealthCheck\Incident\Siren\IncidentSirenCollection;
use TonicHealthCheck\Incident\Siren\NotificationType\EmailNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\RequestNotificationType;

/**
 * Class IncidentEventSubscriber
 * @package TonicHealthCheck\Incident
 */
class IncidentEventSubscriber implements EventSubscriber
{
    protected static $typeEventPolitic =[
        IncidentInterface::TYPE_URGENT => [
            EmailNotificationType::class,
            FileNotificationType::class,
            RequestNotificationType::class,
        ],
        IncidentInterface::TYPE_WARNING => [
            EmailNotificationType::class,
            RequestNotificationType::class,
        ],
        IncidentInterface::TYPE_MINOR => [
            EmailNotificationType::class,
            RequestNotificationType::class,
        ],
    ];

    /**
     * @var IncidentSirenCollection
     */
    private $incidentSirenCollection;

    /**
     * IncidentHandler constructor.
     * @param IncidentSirenCollection $incidentSirenC
     */
    public function __construct($incidentSirenC)
    {
        $this->setIncidentSirenCollection($incidentSirenC);
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preUpdate,
        );
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {

        $entity = $args->getObject();

        if ($entity instanceof IncidentInterface && $args->hasChangedField('status')) {
            /** @var IncidentSiren $incidentI */
            foreach ($this->getIncidentSirenCollection() as $incidentI) {
                if (isset(static::$typeEventPolitic[$entity->getType()])) {
                    $isNotificationAllow = false;
                    foreach (static::$typeEventPolitic[$entity->getType()] as $notificationType) {
                        if (is_a($incidentI->getNotificationTypeInstance(), $notificationType)) {
                            $isNotificationAllow = true;
                            break;
                        }
                    }

                    if ($isNotificationAllow === true) {

                        $entity->attach($incidentI);
                    }
                }
            }

            $entity->notify();
        }
    }

    /**
     * @return IncidentSirenCollection
     */
    public function getIncidentSirenCollection()
    {
        return $this->incidentSirenCollection;
    }

    /**
     * @param IncidentSirenCollection $incidentSiren
     */
    protected function setIncidentSirenCollection(IncidentSirenCollection $incidentSiren)
    {
        $this->incidentSirenCollection = $incidentSiren;
    }
}
