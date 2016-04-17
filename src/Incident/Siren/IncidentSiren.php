<?php

namespace TonicHealthCheck\Incident\Siren;

use Exception;
use SplSubject;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\NotificationTypeInterface;
use TonicHealthCheck\Incident\Siren\Subject\Subject;
use TonicHealthCheck\Incident\Siren\Subject\SubjectCollection;
use TonicHealthCheck\Incident\Siren\Subject\SubjectInterface;

/**
 * Class IncidentSiren
 */
class IncidentSiren implements \SplObserver
{
    const FILE_NAME_ALARM_TRIGGER = 'alarm_triger.data';

    /**
     * @var NotificationTypeInterface
     */
    protected $notificationTypeI;

    /**
     * email or tel numbers
     *
     * @var SubjectCollection
     */
    protected $subjects;

    /**
     * IncidentSiren constructor.
     *
     * @param NotificationTypeInterface $notificationTypeI
     * @param null|SubjectCollection    $subjects
     */
    public function __construct(NotificationTypeInterface $notificationTypeI, SubjectCollection $subjects = null)
    {
        $this->setNotificationTypeI($notificationTypeI);
        if (null === $subjects) {
            $subjects = new SubjectCollection();
        }
        $this->setSubjects($subjects);
    }

    /**
     * Receive update from subject
     *
     * @link http://php.net/manual/en/splobserver.update.php
     *
     * @param SplSubject $subject <p>
     *                            The <b>SplSubject</b> notifying the observer of an update.
     *                            </p>
     *
     * @return void
     *
     * @since 5.1.0
     */
    public function update(SplSubject $subject)
    {
        if ($subject instanceof IncidentInterface) {
            /** @var IncidentInterface $subject */
                $this->notify($subject);
        }
    }

    /**
     * @param IncidentInterface $incident
     * @param null|array        $subjects
     */
    public function notify(IncidentInterface $incident, $subjects = null)
    {
        $subjects = null === $subjects ? $this->getSubjects() : $subjects;
        /** @var SubjectInterface $subject */
        foreach ($subjects as $subject) {
            try {
                $this->notifySubject($incident, $subject);
            } catch (Exception $e) {
                $this->showUserError($e);
            }
        }
    }

    /**
     * @return NotificationTypeInterface
     */
    public function getNotificationTypeI()
    {
        return $this->notificationTypeI;
    }

    /**
     * @return SubjectCollection
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * @param SubjectCollection $subjects
     */
    protected function setSubjects(SubjectCollection $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @param NotificationTypeInterface $notificationTypeI
     */
    protected function setNotificationTypeI(NotificationTypeInterface $notificationTypeI)
    {
        $this->notificationTypeI = $notificationTypeI;
    }

    /**
     * @param IncidentInterface $incident
     * @param Subject           $subject
     */
    protected function notifySubject(IncidentInterface $incident, $subject)
    {
        if (null === $subject->getSchedule() || $subject->getSchedule()->isDue()) {
            $this->getNotificationTypeI()->notify($subject, $incident);
        }
    }

    /**
     * @param Exception $errorException
     */
    protected function showUserError(Exception $errorException)
    {
        user_error($errorException->getMessage(), E_USER_WARNING);
    }// @codeCoverageIgnore
}
