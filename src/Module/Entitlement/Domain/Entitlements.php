<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain;

use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

interface Entitlements
{
    public function save(Entitlement $entitlement): void;

    public function findOneById(UuidInterface $entitlementId): Option;
}