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

        $incidentOk = $this->createIncidentMock();

        $incidentOk
            ->expects(self::any())
            ->method('getStatus')
            ->willReturn(IncidentInterface::STATUS_OK);

        $subject = $this->createSubject('target', '* * * * *');

        $event = new Event();
        $event->serviceKey = $this->serviceKey;
        $event->description = $incident->getIdent();
        $event->incidentKey = 'hci_0';
        $event->details = [
            'log' => $incident->getMessage(),
            'status' => $incident->getStatus(),
            'type' => $incident->getType(),
            'id' => $incident->getId(),
        ];

        $this->eventClientMock
            ->expects(self::at(0))
            ->method('post')
            ->with($event);

        $event2 = clone $event;
        $event2->details = null;
        $event2->description = null;
        $event2->eventType = Event::EVENT_TYPE_RESOLVE;

        $this->eventClientMock
            ->expects(self::at(1))
            ->method('post')
            ->with($event2);



        $this->pagerDutyNType->notify($subject, $incident);
        $this->pagerDutyNType->notify($subject, $incidentOk);

    }

    /**
     * Test request notify update.
     */
    public function testNotifyUpdate()
    {
    }
}
