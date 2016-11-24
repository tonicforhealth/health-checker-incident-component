<?php

namespace TonicHealthCheck\Incident;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;;
use Doctrine\ORM\UnitOfWork;
use TonicHealthCheck\Incident\Siren\IncidentSiren;
use TonicHealthCheck\Incident\Siren\IncidentSirenCollection;
use TonicHealthCheck\Incident\Siren\NotificationType\EmailNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\NotificationTypeInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\PagerDutyNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\RequestNotificationType;

/**
 * Class IncidentEventSubscriber.
 */
class IncidentEventSubscriber implements EventSubscriber
{
    protected static $typeEventPolitic = [
        IncidentInterface::TYPE_URGENT => [
            EmailNotificationType::class,
            FileNotificationType::class,
            RequestNotificationType::class,
            PagerDutyNotificationType::class,
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
     *
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
            Events::onFlush,
        );
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityM = $eventArgs->getEntityManager();
        $uow = $entityM->getUnitOfWork();
        $updates = $uow->getScheduledEntityUpdates();

        foreach ($updates as $entity) {
            /** @var IncidentInterface $entity */
            if ($entity instanceof IncidentInterface) {
                $this->preFlushIncident($uow, $entity);
            }
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

    /**
     * @param IncidentInterface $entity
     */
    protected function preUpdateIncidentStatus(IncidentInterface $entity)
    {
        /** @var IncidentSiren $incidentI */
        foreach ($this->getIncidentSirenCollection() as $incidentI) {
            if (isset(static::$typeEventPolitic[$entity->getType()])
                && $this->checkIsNotificationAllow($entity->getType(), $incidentI->getNotificationTypeI())
            ) {
                $entity->attach($incidentI);
            } else {
                $entity->detach($incidentI);
            }
        }
    }

    /**
     * @param string $type
     * @param NotificationTypeInterface $notificationTypeI
     *
     * @return bool
     */
    protected function checkIsNotificationAllow($type, NotificationTypeInterface $notificationTypeI)
    {
        $isNotificationAllow = false;

        foreach (static::$typeEventPolitic[$type] as $notificationType) {
            if (is_a($notificationTypeI, $notificationType)) {
                $isNotificationAllow = true;
                break;
            }
        }

        return $isNotificationAllow;
    }

    /**
     * @param UnitOfWork $uow
     * @param IncidentInterface $entity
     */
    protected function preFlushIncident(UnitOfWork $uow, IncidentInterface $entity)
    {
        $changeSet = $uow->getEntityChangeSet($entity);
        if (array_key_exists('status', $changeSet) || array_key_exists('type', $changeSet)) {
            $this->preUpdateIncidentStatus($entity);
            $entity->notify();
        }
    }
}
