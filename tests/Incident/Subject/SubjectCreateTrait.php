<?php

namespace TonicHealthCheck\Test\Incident\Subject;

use Cron\CronExpression;
use TonicHealthCheck\Incident\Siren\Subject\Subject;

trait SubjectCreateTrait
{
    /**
     * @param $target
     * @param $cronExp
     *
     * @return Subject
     */
    private function createSubject($target, $cronExp)
    {
        return new Subject($target, CronExpression::factory($cronExp));
    }
}
