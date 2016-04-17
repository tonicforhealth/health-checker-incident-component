<?php

namespace TonicHealthCheck\Test\Incident\Siren\NotificationType;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;
use TonicHealthCheck\Incident\IncidentInterface;
use TonicHealthCheck\Incident\Siren\NotificationType\EmailNotificationType;
use TonicHealthCheck\Test\Incident\Subject\SubjectCreateTrait;

/**
 * Class EmailNotificationTypeTest
 */
class EmailNotificationTypeTest extends PHPUnit_Framework_TestCase
{
    use SubjectCreateTrait;
    /**
     * @var string
     */
    private $from = 'test@test.com';

    /**
     * @var string
     */
    private $fromName = 'Tester Test';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Swift_Mailer;
     */
    private $mailer;

    /**
     * @var EmailNotificationType;
     */
    private $mailNType;

    /**
     * set base env for test EmailNotificationTypeTest
     */
    public function setUp()
    {
        $this->setMailer($this
            ->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock()
        );

        $this->setMailNType(
            new EmailNotificationType(
                $this->getMailer(),
                $this->getFrom(),
                $this->getFromName()
            )
        );
    }

    /**
     * test email notify ok
     */
    public function testNotify()
    {
        $incident = $this->getMockBuilder(IncidentInterface::class)->getMock();

        $incident->expects($this->any())->method('getStatus')->willReturn(IncidentInterface::STATUS_OK + 1);

        $subject = $this->createSubject('subject@test.com', '* * * * *');

        $mailerTransport = $this->getMock(Swift_Transport::class);

        $mailerTransport->expects($this->once())->method('stop');

        $this
            ->getMailer()
            ->expects($this->any())
            ->method('getTransport')
            ->willReturn($mailerTransport);

        $message = $this->createMessage($subject, $incident);

        $this
            ->getMailer()
            ->expects($this->any())
            ->method('send')
            ->with($this->callback($this->isMessageSame($message)));

        $this->getMailNType()->notify($subject, $incident);
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    protected function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param Swift_Mailer $mailer
     */
    protected function setMailer(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return EmailNotificationType
     */
    protected function getMailNType()
    {
        return $this->mailNType;
    }

    /**
     * @param EmailNotificationType $mailNType
     */
    protected function setMailNType(EmailNotificationType $mailNType)
    {
        $this->mailNType = $mailNType;
    }

    /**
     * @param Swift_Message $message
     * @return \Closure
     */
    protected function isMessageSame(Swift_Message $message)
    {
        return function (Swift_Message $messageI) use ($message) {
            return $messageI->getFrom() == $message->getFrom()
            && $messageI->getTo() == $message->getTo()
            && $messageI->getSubject() == $message->getSubject()
            && $messageI->getBody() == $message->getBody();
        };
    }

    /**
     * @param $subject
     * @param $incident
     * @return Swift_Message
     */
    protected function createMessage($subject, $incident)
    {
        $message = Swift_Message::newInstance()
            ->setTo($subject->getTarget())
            ->setFrom($this->getFrom(), $this->getFromName())
            ->setSubject(sprintf(EmailNotificationType::EMAIL_SUBJECT_T, $incident->getIdent()))
            ->setBody(sprintf(EmailNotificationType::EMAIL_BODY_T, $incident->getMessage()));

        return $message;
    }
}
