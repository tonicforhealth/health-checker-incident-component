<?php

namespace TonicHealthCheck\Incident\Siren\NotificationType;

use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\Subject\SubjectInterface;

/**
 * Interface NotificationTypeInterface.
 */
interface NotificationTypeInterface
{
    /**
     * @param mixed             $subject
     * @param IncidentInterface $incident
     *
     * @return
     */
    public function notify(SubjectInterface $subject, IncidentInterface $incident);
}
