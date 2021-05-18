<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain;

use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

interface Resources
{
    public function save(Resource $resource): void;

    /**
     * @return Option<Resource>
     */
    public function findOneById(UuidInterface $resourceId): Option;
}
