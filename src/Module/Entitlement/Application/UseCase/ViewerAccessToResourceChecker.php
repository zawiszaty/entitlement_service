<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\UseCase;

use App\Module\Entitlement\Domain\Exception\MissingResource;
use App\Module\Entitlement\Domain\Exception\MissingViewer;
use App\Module\Entitlement\Domain\Resources;
use App\Module\Entitlement\Domain\Viewers;
use Ramsey\Uuid\UuidInterface;

class ViewerAccessToResourceChecker
{
    private Viewers $viewers;
    private Resources $resources;

    public function __construct(Viewers $viewers, Resources $resources)
    {
        $this->viewers = $viewers;
        $this->resources = $resources;
    }

    public function canAccess(UuidInterface $viewerId, UuidInterface $resourceId): bool
    {
        $viewer = $this->viewers->findOneById($viewerId);

        if ($viewer->isEmpty()) {
            throw MissingViewer::create($viewerId);
        }
        $resource = $this->resources->findOneById($resourceId);

        if ($resource->isEmpty()) {
            throw MissingResource::create($resourceId);
        }
        return $viewer->get()->canAccessResource($resource->get());
    }
}