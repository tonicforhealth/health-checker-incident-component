<?php

namespace TonicHealthCheck\Incident\Siren\Subject;

use Cron\CronExpression;

/**
 * Class Subject
 */
interface SubjectInterface
{
    /**
     * @return string
     */
    public function getTarget();

    /**
     * @return CronExpression
     */
    public function getSchedule();

    /**
     * @return string
     */
    public function __toString();
}
