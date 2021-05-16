<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\UseCase;

use App\Module\Entitlement\Domain\Entitlements;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Domain\Resources;
use Munus\Collection\GenericList;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ResourceCreator
{
    private Resources $resources;

    private Entitlements $entitlements;

    public function __construct(Resources $resources, Entitlements $entitlements)
    {
        $this->resources = $resources;
        $this->entitlements = $entitlements;
    }

    public function create(string $name, string $category, GenericList $entitlementsIds): void
    {
        $entitlementsCollection = GenericList::empty();
        $entitlementsIds->forAll(function (UuidInterface $id) use (&$entitlementsCollection) {
            $entitlement = $this->entitlements->findOneById($id);

            if ($entitlement->isEmpty()) {
                throw MissingEntitlement::create($id);
            }
            $entitlementsCollection = $entitlementsCollection->append($entitlement->get());
        });
        $resource = new Resource(Uuid::uuid4(), $name, $category, $entitlementsCollection);
        $this->resources->save($resource);
    }
}