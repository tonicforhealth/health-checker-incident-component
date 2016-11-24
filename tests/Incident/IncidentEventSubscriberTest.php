<?php

namespace TonicHealthCheck\Test\Incident;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use PHPUnit_Framework_TestCase;
use TonicHealthCheck\Incident\IncidentEventSubscriber;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\IncidentSiren;
use TonicHealthCheck\Incident\Siren\IncidentSirenCollection;
use TonicHealthCheck\Incident\Siren\NotificationType\EmailNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationType;
use TonicHealthCheck\Incident\Siren\NotificationType\RequestNotificationType;

/**
 * Class IncidentEventSubscriber.
 */
class IncidentEventSubscriberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IncidentEventSubscriber;
     */
    private $incidentEventSubscriber;

    /**
     * @var IncidentSirenCollection;
     */
    private $incidentSirenC;

    /**
     * set up base dependency.
     */
    public function setUp()
    {
        $this->setIncidentSirenC(new IncidentSirenCollection());

        $this->getIncidentSirenC()->add($this->createSirenMock(FileNotificationType::class));
        $this->getIncidentSirenC()->add($this->createSirenMock(EmailNotificationType::class));
        $this->getIncidentSirenC()->add($this->createSirenMock(RequestNotificationType::class));

        $this->setIncidentEventSubscriber(
            new IncidentEventSubscriber($this->getIncidentSirenC())
        );
    }

    /**
     * Test onFlush.
     */
    public function testOnFlush()
    {
        $argsMock = $this->createEventArgsMock(OnFlushEventArgs::class);

        $entity = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $entityMinor = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $entityMinor
            ->expects($this->any())
            ->method('getType')
            ->willReturn(IncidentInterface::TYPE_MINOR);


        $unitOfWork = $this->getMockBuilder(UnitOfWork::class)
            ->disableOriginalConstructor()
            ->getMock();

        $unitOfWork
            ->expects($this->once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([$entity, $entityMinor]);

        $unitOfWork
            ->expects($this->any())
            ->method('getEntityChangeSet')
            ->willReturn(['status' => 234]);

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $argsMock
            ->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $this->setUpExpectsForEntity($entity);

        $this->getIncidentEventSubscriber()->onFlush($argsMock);
    }

    /**
     * Test get subscribed events.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            $this->getIncidentEventSubscriber()->getSubscribedEvents(),
            [
                Events::onFlush,
            ]
        );
    }

    /**
     * @return IncidentEventSubscriber
     */
    protected function getIncidentEventSubscriber()
    {
        return $this->incidentEventSubscriber;
    }

    /**
     * @param IncidentEventSubscriber $incidentEventSub
     */
    protected function setIncidentEventSubscriber(IncidentEventSubscriber $incidentEventSub)
    {
        $this->incidentEventSubscriber = $incidentEventSub;
    }

    /**
     * @return IncidentSirenCollection
     */
    protected function getIncidentSirenC()
    {
        return $this->incidentSirenC;
    }

    /**
     * @param IncidentSirenCollection $incidentSirenC
     */
    protected function setIncidentSirenC(IncidentSirenCollection $incidentSirenC)
    {
        $this->incidentSirenC = $incidentSirenC;
    }

    /**
     * @param $sirenNType
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|IncidentSiren
     */
    private function createSirenMock($sirenNType)
    {
        $incidentSiren = $this->getMockBuilder(IncidentSiren::class)
            ->disableOriginalConstructor()->getMock();

        $incidentSiren
            ->expects($this->any())
            ->method('getNotificationTypeI')
            ->willReturn(
                $this
                    ->getMockBuilder($sirenNType)
                    ->disableOriginalConstructor()
                    ->getMock()
            );

        return $incidentSiren;
    }

    /**
     * @param $eventArgsClassName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createEventArgsMock($eventArgsClassName)
    {
        $argsMock = $this->getMockBuilder($eventArgsClassName)
            ->disableOriginalConstructor()
            ->getMock();

        return $argsMock;
    }

    /**
     * @param $entity
     */
    protected function setUpExpectsForEntity($entity)
    {
        $entity
            ->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $entity->expects($this->once())->method('notify');
        $entity
            ->expects($this->any())
            ->method('getType')
            ->willReturn(IncidentInterface::TYPE_URGENT);
    }
}
