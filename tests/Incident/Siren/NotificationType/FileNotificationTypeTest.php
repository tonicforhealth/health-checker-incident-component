<?php

namespace TonicHealthCheck\Test\Incident\Siren\NotificationType;

use PHPUnit_Framework_TestCase;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationType;
use TonicHealthCheck\Test\Incident\Subject\SubjectCreateTrait;

/**
 * Class FileNotificationTypeTest
 */
class FileNotificationTypeTest extends PHPUnit_Framework_TestCase
{
    use SubjectCreateTrait;

    /**
     * @var string
     */
    private $pathMessage;

    /**
     * @var FileNotificationType;
     */
    private $fileNType;


    /**
     * set up file notification env
     */
    public function setUp()
    {
        $this->setPathMessage(sys_get_temp_dir());

        $this->setFileNType(new FileNotificationType($this->getPathMessage()));
    }

    /**
     * test email notify ok
     */
    public function testNotify()
    {
        $incident = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $incident->expects($this->any())->method('getStatus')->willReturn(IncidentInterface::STATUS_OK + 1);

        $subject = $this->createSubject('subject@test.com', '* * * * *');

        $this->getFileNType()->notify($subject, $incident);

        $this->assertStringEqualsFile($this->getFileNType()->getSubjectNotifyFilePath($subject), $incident->getMessage());
    }

    /**
     * Test Message Not Writable throw Exception
     * @expectedException \TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationTypeException
     * @expectedExceptionMessage Notification directory /test343/tes232 doesn't writable
     */
    public function testPathMessageNotWritable()
    {
        $this->getFileNType()->setPathMessage('/test343/tes232');
    }
    /**
     * @return FileNotificationType
     */
    protected function getFileNType()
    {
        return $this->fileNType;
    }

    /**
     * @param FileNotificationType $fileNType
     */
    protected function setFileNType(FileNotificationType $fileNType)
    {
        $this->fileNType = $fileNType;
    }

    /**
     * @return mixed
     */
    protected function getPathMessage()
    {
        return $this->pathMessage;
    }

    /**
     * @param mixed $pathMessage
     */
    protected function setPathMessage($pathMessage)
    {
        $this->pathMessage = $pathMessage;
    }
}
