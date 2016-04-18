<?php

namespace TonicHealthCheck\Incident\Siren;

use Collections\Collection;

/**
 * Class IncidentSirenCollection
 */
class IncidentSirenCollection extends Collection
{
    const OBJECT_CLASS = IncidentSiren::class;

    /**
     * IncidentCollection constructor.
     */
    public function __construct()
    {
        parent::__construct(static::OBJECT_CLASS);
    }
}
