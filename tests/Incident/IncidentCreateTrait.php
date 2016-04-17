<?php

namespace TonicHealthCheck\Test\Incident;

use PHPUnit_Framework_MockObject_MockObject;
use TonicHealthCheck\Incident\IncidentInterface;

trait IncidentCreateTrait
{
    /**
     * @param null|string $iIdent
     * @param null|string $iMessage
     *
     * @return PHPUnit_Framework_MockObject_MockObject|IncidentInterface
     */
    protected function createIncidentMock($iIdent = 'node_name.some_check', $iMessage = 'check error')
    {
        $incident = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $incident
            ->expects($this->any())
            ->method('getIdent')
            ->willReturn($iIdent);

        $incident
            ->expects($this->any())
            ->method('getMessage')
            ->willReturn($iMessage);

        return $incident;
    }
}
