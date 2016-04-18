<?php

namespace TonicHealthCheck\Incident\Siren\NotificationType;

/**
 * Class FileNotificationTypeExeption
 */
class FileNotificationTypeException extends \Exception
{
    /**
     * @param string $dir
     *
     * @return FileNotificationTypeException
     */
    public static function dirForMessageDoesNotWritable($dir)
    {
        return new self(sprintf('Notification directory %s doesn\'t writable', $dir));
    }
}
