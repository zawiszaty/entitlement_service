<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain;

use DateTime;
use Munus\Collection\GenericList;
use Ramsey\Uuid\UuidInterface;

final class Entitlement
{
    private UuidInterface $id;

    /** @var GenericList<Resource> */
    private GenericList $resources;

    private ?DateTime $expiresAt; //@TODO value object

    /** @var GenericList<Viewer> */
    private GenericList $viewers;

    /** @var GenericList<Entitlement> */
    private GenericList $childrenEntitlements;

    /**
     * Entitlement constructor.
     *
     * @param GenericList<Resource>|null    $resources
     * @param GenericList<Viewer>|null      $viewers
     * @param GenericList<Entitlement>|null $childrenEntitlements
     */
    public function __construct(UuidInterface $id, DateTime $expiresAt = null, GenericList $resources = null, GenericList $viewers = null, GenericList $childrenEntitlements = null)
    {
        $this->id = $id;
        $this->expiresAt = $expiresAt;
        $this->resources = $resources ?: GenericList::empty();
        $this->viewers = $viewers ?: GenericList::empty();
        $this->childrenEntitlements = $childrenEntitlements ?: GenericList::empty();
    }

    public function addResource(Resource $resource): void
    {
        if ($this->checkIfResourceExist($resource)) {
            return;
        }
        $this->resources = $this->resources->append($resource);
        $resource->addEntitlement($this);
    }

    public function checkIfResourceExist(Resource $resource): bool
    {
        if ($this->resources->exists(static function (Resource $existingResource) use ($resource) {
            return $existingResource->getId()->equals($resource->getId());
        })) {
            return true;
        }

        return $this->childrenEntitlements->exists(static function (Entitlement $childrenEntitlement) use ($resource) {
            return $childrenEntitlement->checkIfResourceExist($resource);
        });
    }

    public function checkAccessToResource(Resource $resource): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->resources->exists(static function (Resource $existingResource) use ($resource) {
            return $existingResource->getId()->equals($resource->getId());
        })) {
            return true;
        }

        return $this->childrenEntitlements->exists(static function (Entitlement $childrenEntitlement) use ($resource) {
            return $childrenEntitlement->checkAccessToResource($resource);
        });
    }

    public function isExpired(): bool
    {
        return $this->expiresAt && $this->expiresAt < new \DateTime();
    }

    public function addViewer(Viewer $viewer): void
    {
        if ($this->checkIfViewerExist($viewer)) {
            return;
        }
        $this->viewers = $this->viewers->append($viewer);
        $viewer->addEntitlement($this);
    }

    private function checkIfViewerExist(Viewer $viewer): bool
    {
        return $this->viewers->exists(static function (Viewer $existingViewer) use ($viewer) {
            return $existingViewer->getId()->equals($viewer->getId());
        });
    }

    public function addChildrenEntitlement(Entitlement $entitlement): void
    {
        if ($this->checkIfEntitlementExist($entitlement)) {
            return;
        }
        $this->childrenEntitlements = $this->childrenEntitlements->append($entitlement);
    }

    private function checkIfEntitlementExist(Entitlement $entitlement): bool
    {
        return $this->childrenEntitlements->exists(static function (Entitlement $existingEntitlement) use ($entitlement) {
            return $existingEntitlement->getId()->equals($entitlement->getId());
        });
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return GenericList<Resource>
     */
    public function getResources(): GenericList
    {
        return $this->resources;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }
}
