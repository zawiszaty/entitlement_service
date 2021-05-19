<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain;

use Munus\Collection\GenericList;
use Ramsey\Uuid\UuidInterface;

final class Resource
{
    private UuidInterface $id;

    private string $name;

    private string $category;

    /** @var GenericList<Entitlement> */
    private GenericList $entitlements;

    /**
     * @param GenericList<Entitlement>|null $entitlements
     */
    public function __construct(UuidInterface $id, string $name, string $category, GenericList $entitlements = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->entitlements = $entitlements ?: GenericList::empty();
    }

    public function addEntitlement(Entitlement $entitlement): void
    {
        if ($this->checkIfEntitlementExist($entitlement)) {
            return;
        }
        $this->entitlements = $this->entitlements->append($entitlement);
        $entitlement->addResource($this);
    }

    private function checkIfEntitlementExist(Entitlement $entitlement): bool
    {
        return $this->entitlements->exists(static function (Entitlement $existingEntitlement) use ($entitlement) {
            return $existingEntitlement->getId()->equals($entitlement->getId());
        });
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return GenericList<Entitlement>
     */
    public function getEntitlements(): GenericList
    {
        return $this->entitlements;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}
