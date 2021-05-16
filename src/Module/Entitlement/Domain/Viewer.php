<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain;

use Munus\Collection\GenericList;
use Ramsey\Uuid\UuidInterface;

final class Viewer
{
    private UuidInterface $id;

    /** @var GenericList<Entitlement> */
    private GenericList $entitlements;

    public function __construct(UuidInterface $id, GenericList $entitlements = null)
    {
        $this->id = $id;
        $this->entitlements = $entitlements ?: GenericList::empty();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function canAccessResource(Resource $resource): bool
    {
        return $this->entitlements->exists(function (Entitlement $entitlement) use ($resource) {
            return $entitlement->checkAccessToResource($resource);
        });
    }

    public function addEntitlement(Entitlement $entitlement): void
    {
        if ($this->checkIfEntitlementExist($entitlement)) {
            return;
        }
        $this->entitlements = $this->entitlements->append($entitlement);
        $entitlement->addViewer($this);
    }

    private function checkIfEntitlementExist(Entitlement $entitlement): bool
    {
        return $this->entitlements->exists(static function (Entitlement $existingEntitlement) use ($entitlement) {
            return $existingEntitlement->getId()->equals($entitlement->getId());
        });
    }

    public function getEntitlements(): GenericList
    {
        return $this->entitlements;
    }
}