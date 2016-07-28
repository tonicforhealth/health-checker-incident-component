<?php

namespace TonicHealthCheck\Test\Incident\Siren\NotificationType;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use TonicForHealth\PagerDutyClient\Client\EventClient;
use TonicForHealth\PagerDutyClient\Entity\Event\Event;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\PagerDutyNotificationType;
use TonicHealthCheck\Test\Incident\IncidentCreateTrait;
use TonicHealthCheck\Test\Incident\Subject\SubjectCreateTrait;

/**
 * Class RequestNotificationTypeTest.
 */
class PagerDutyNotificationTypeTest extends PHPUnit_Framework_TestCase
{
    use SubjectCreateTrait;
    use IncidentCreateTrait;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|EventClient
     */
    protected $eventClientMock;

    /**
     * @var PagerDutyNotificationType;
     */
    protected $pagerDutyNType;

    /**
     * @var string
     */
    protected $serviceKey;

    /**
     * set up base env Request type test.
     */
    public function setUp()
    {
        $this->serviceKey = '5b2fb01f1f2257dbbde64469977261de';

        $this->eventClientMock = $this
            ->getMockBuilder(EventClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pagerDutyNType = new PagerDutyNotificationType(
            $this->eventClientMock,
            $this->serviceKey
        );
    }

    /**
     * Test request notify create.
     */
    public function testNotifyCreate()
    {
        self::assertEquals($this->serviceKey, $this->pagerDutyNType->getServiceKey());

        $incident = $this->createIncidentMock();

        $incident
            ->expects(self::any())
            ->method('getStatus')
            ->willReturn(IncidentInterface::STATUS_OK + 1);

        $subject = $this->createSubject('target', '* * * * *');

        $event = new Event();
        $event->serviceKey = $this->serviceKey;
        $event->description = $incident->getMessage();

        $this->eventClientMock
            ->expects(self::once())
            ->method('post')
            ->with($event);

        $this->pagerDutyNType->notify($subject, $incident);
    }

    /**
     * Test request notify update.
     */
    public function testNotifyUpdate()
    {
    }
}
