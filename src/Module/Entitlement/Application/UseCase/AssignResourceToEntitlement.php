<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\UseCase;

use App\Module\Entitlement\Domain\Entitlements;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Exception\MissingResource;
use App\Module\Entitlement\Domain\Resources;
use Ramsey\Uuid\UuidInterface;

class AssignResourceToEntitlement
{
    private Entitlements $entitlements;

    private Resources $resources;

    public function __construct(Entitlements $entitlements, Resources $resources)
    {
        $this->entitlements = $entitlements;
        $this->resources = $resources;
    }

    public function assign(UuidInterface $entitlementId, UuidInterface $resourceId): void
    {
        $entitlement = $this->entitlements->findOneById($entitlementId);

        if ($entitlement->isEmpty()) {
            throw MissingEntitlement::create($entitlementId);
        }
        $resource = $this->resources->findOneById($resourceId);

        if ($resource->isEmpty()) {
            throw MissingResource::create($resourceId);
        }
        $entitlement->get()->addResource($resource->get());
        $this->entitlements->save($entitlement->get());
    }
}