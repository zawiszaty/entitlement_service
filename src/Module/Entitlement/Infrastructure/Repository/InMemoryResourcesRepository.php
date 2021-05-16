<?php

declare(strict_types=1);


namespace App\Module\Entitlement\Infrastructure\Repository;


use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Domain\Resources;
use Munus\Collection\Map;
use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

class InMemoryResourcesRepository implements Resources
{
    /** @var Map<Resource> */
    private Map $resources;

    public function __construct()
    {
        $this->resources = Map::empty();
    }

    public function save(Resource $resource): void
    {
        $this->resources = $this->resources->put($resource->getId()->toString(), $resource);
    }

    public function findOneById(UuidInterface $resourceId): Option
    {
        return $this->resources->get($resourceId->toString());
    }

    public function getAll(): Map
    {
        return $this->resources;
    }
}