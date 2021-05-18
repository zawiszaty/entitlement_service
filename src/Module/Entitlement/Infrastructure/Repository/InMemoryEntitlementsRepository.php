<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Infrastructure\Repository;

use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Entitlements;
use Munus\Collection\Map;
use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

class InMemoryEntitlementsRepository implements Entitlements
{
    /** @var Map<string, Entitlement> */
    private Map $entitlements;

    public function __construct()
    {
        $this->entitlements = Map::empty();
    }

    public function save(Entitlement $entitlement): void
    {
        $this->entitlements = $this->entitlements->put($entitlement->getId()->toString(), $entitlement);
    }

    /**
     * @return Option<Entitlement>
     */
    public function findOneById(UuidInterface $entitlementId): Option
    {
        return $this->entitlements->get($entitlementId->toString());
    }
}
