<?php

namespace TonicHealthCheck\Test\Incident\Siren\NotificationType;

use Cron\CronExpression;
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Mock\Client as MockClient;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\RequestNotificationType;
use TonicHealthCheck\Incident\Siren\Subject\Subject;
use TonicHealthCheck\Test\Incident\Subject\SubjectCreateTrait;

/**
 * Class RequestNotificationTypeTest
 */
class RequestNotificationTypeTest extends PHPUnit_Framework_TestCase
{
    use SubjectCreateTrait;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|MockClient
     */
    private $mockClient;

    /**
     * @var HttpMethodsClient
     */
    private $httpClient;

    /**
     * @var RequestNotificationType;
     */
    private $requestNType;

    /**
     * @var string
     */
    private $resourceUrl = '/incident';

    /**
     * set up base env Request type test
     */
    public function setUp()
    {
        $this->setMockClient(new MockClient());

        $this->setHttpClient(
            $this
                ->getMockBuilder(HttpMethodsClient::class)
                ->setConstructorArgs([
                    $this->getMockClient(),
                    MessageFactoryDiscovery::find(),
                ])
                ->enableProxyingToOriginalMethods()
                ->getMock()
        );

        $this->setRequestNType(new RequestNotificationType(
            $this->getHttpClient(),
            $this->getResourceUrl()
        ));
    }

    /**
     * Test request notify create
     */
    public function testNotifyCreate()
    {
        $this->assertEquals($this->getResourceUrl(), $this->getRequestNType()->getResourceUrl());

        $incident = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $incident
            ->expects($this->any())
            ->method('getStatus')
            ->willReturn(IncidentInterface::STATUS_OK + 1);

        $subject = $this->createSubject('target', '* * * * *');

        $streamMock = $this->getMock(StreamInterface::class);

        $streamMock
            ->expects($this->once())
            ->method('getContents')
            ->willReturn('{"data":{"id":22}}');

        $response = $this->getMock(ResponseInterface::class);

        $response->expects($this->once())->method('getBody')->willReturn($streamMock);

        $this->getMockClient()->addResponse($response);

        $this->getHttpClient()
            ->expects($this->once())
            ->method('post')
            ->with(
                $this->identicalTo('target/incident'),
                $this->identicalTo(['Content-type' => 'application/json']),
                $this->identicalTo('{"name":null,"message":null,"status":1,"visible":1}')
            );

        $this->getRequestNType()->notify($subject, $incident);
    }

    /**
     * Test request notify update
     */
    public function testNotifyUpdate()
    {
        $incident = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $incident->expects($this->any())->method('getStatus')->willReturn(IncidentInterface::STATUS_OK);

        $subject = $this->createSubject('target', '* * * * *');

        $this->getHttpClient()
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo('target/incident/'),
                $this->identicalTo(['Content-type' => 'application/json']),
                $this->identicalTo('{"status":4}')
            );

        $this->getRequestNType()->notify($subject, $incident);
    }

    /**
     * @return MockClient
     */
    protected function getMockClient()
    {
        return $this->mockClient;
    }

    /**
     * @param MockClient $mockClient
     */
    protected function setMockClient(MockClient $mockClient)
    {
        $this->mockClient = $mockClient;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|HttpMethodsClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpMethodsClient $httpClient
     */
    protected function setHttpClient(HttpMethodsClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return string
     */
    protected function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * @return RequestNotificationType
     */
    protected function getRequestNType()
    {
        return $this->requestNType;
    }

    /**
     * @param RequestNotificationType $requestNType
     */
    protected function setRequestNType(RequestNotificationType $requestNType)
    {
        $this->requestNType = $requestNType;
    }
}
