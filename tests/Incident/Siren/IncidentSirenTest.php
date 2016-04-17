<?php

namespace TonicHealthCheck\Test\Incident\Siren;

use Cron\CronExpression;
use Exception;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\IncidentSiren;
use TonicHealthCheck\Incident\Siren\NotificationType\NotificationTypeInterface;
use TonicHealthCheck\Incident\Siren\Subject\SubjectCollection;
use TonicHealthCheck\Test\Incident\Subject\SubjectCreateTrait;

/**
 * Class IncidentEventSubscriber
 */
class IncidentSirenTest extends PHPUnit_Framework_TestCase
{
    use SubjectCreateTrait;

    /**
     * @var IncidentSiren;
     */
    private $incidentSiren;

    /**
     * Test constructor create new SubjectCollection if $subjects skiped
     */
    public function testConstructorNullSubject()
    {
        $nTypeIMock = $this->getMock(NotificationTypeInterface::class);

        $this->setIncidentSiren(new IncidentSiren($nTypeIMock));

        $this->assertInstanceOf(SubjectCollection::class, $this->getIncidentSiren()->getSubjects());
    }

    /**
     * test receive update from subject
     */
    public function testUpdate()
    {
        $nTypeIMock = $this->getMock(NotificationTypeInterface::class);

        $subjectC = new SubjectCollection();

        $subjectC->add($this->createSubject('target', '* * * * *'));

        $this->setIncidentSiren(new IncidentSiren($nTypeIMock, $subjectC));

        $incidentMock = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $this->getIncidentSiren()->update($incidentMock);
    }

    /**
     * test receive update from subject
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage Test exception message
     */
    public function testNotifyCatchException()
    {
        $nTypeIMock = $this->getMock(NotificationTypeInterface::class);

        $subjectC = new SubjectCollection();

        $subjectC->add($this->createSubject('terget', '* * * * *'));

        $this->setIncidentSiren(new IncidentSiren($nTypeIMock, $subjectC));

        $incidentMock = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $errorException = new Exception('Test exception message', 3234);

        $nTypeIMock->expects($this->once())->method('notify')->willThrowException($errorException);

        $this->getIncidentSiren()->notify($incidentMock);
    }

    /**
     * @return IncidentSiren
     */
    protected function getIncidentSiren()
    {
        return $this->incidentSiren;
    }

    /**
     * @param IncidentSiren $incidentSiren
     */
    protected function setIncidentSiren(IncidentSiren $incidentSiren)
    {
        $this->incidentSiren = $incidentSiren;
    }

}
