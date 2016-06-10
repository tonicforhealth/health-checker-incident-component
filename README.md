# Health checker incident component
[![License](https://img.shields.io/github/license/tonicforhealth/health-checker-incident-component.svg?maxAge=2592000)](LICENSE.md)
[![Build Status](https://travis-ci.org/tonicforhealth/health-checker-incident-component.svg?branch=master)](https://travis-ci.org/tonicforhealth/health-checker-incident-component)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tonicforhealth/health-checker-incident-component/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tonicforhealth/health-checker-incident-component/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tonicforhealth/health-checker-incident-component/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tonicforhealth/health-checker-incident-component/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7a1c4148-5ee0-468d-ad58-f7a639cb6ad2/mini.png)](https://insight.sensiolabs.com/projects/7a1c4148-5ee0-468d-ad58-f7a639cb6ad2)

This repository provides classes for Health checker incident app. These classes are used for different notification types like 
email, cachet, file, etc. All these classes implement NotificationTypeInterface. Also here you can find Subject class that 
IncidentSiren uses like a point for sending notifications.

## Requirements
------------

- PHP 5.5 or higher
- ext-pdo

## Classes
------------

Classes
- TonicHealthCheck\Incident\IncidentEventSubscriber - core class for mediation with incidents. Also this class implements Doctrine\Common\EventSubscriber interface.
- TonicHealthCheck\Incident\Siren\IncidentSiren - for notification subjects.
- TonicHealthCheck\Incident\Siren\IncidentSirenCollection - collection of the IncidentSiren items
- TonicHealthCheck\Incident\Siren\NotificationType\EmailNotificationType - implements email notification.
- TonicHealthCheck\Incident\Siren\NotificationType\FileNotificationType - implements file notification
- TonicHealthCheck\Incident\Siren\NotificationType\RequestNotificationType - implements request notification
- TonicHealthCheck\Incident\Siren\Subject\Subject - implements subjects and is used as a point to notification
- TonicHealthCheck\Incident\Siren\Subject\SubjectCollection - collection of the Subject items


Interfaces

- TonicHealthCheck\Incident\IncidentInterface - interface for notification entity
- TonicHealthCheck\Incident\Siren\Subject\SubjectInterface - interface for subjects
- TonicHealthCheck\Incident\Siren\NotificationType\NotificationTypeInterface - interface for notification types

Other

- TonicHealthCheck\CachetHQ\Authentication\Token - class for cachetHQ authentication 
