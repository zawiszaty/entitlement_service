<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\UseCase;

use App\Module\Entitlement\Domain\Entitlements;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Exception\MissingViewer;
use App\Module\Entitlement\Domain\Viewers;
use Ramsey\Uuid\UuidInterface;

class AssignEntitlementToViewer
{
    private Viewers $viewers;
    private Entitlements $entitlements;

    public function __construct(Viewers $viewers, Entitlements $entitlements)
    {
        $this->viewers = $viewers;
        $this->entitlements = $entitlements;
    }

    public function assign(UuidInterface $viewerId, UuidInterface $entitlementId): void
    {
        $viewer = $this->viewers->findOneById($viewerId);

        if ($viewer->isEmpty()) {
            throw MissingViewer::create($viewerId);
        }
        $entitlement = $this->entitlements->findOneById($entitlementId);

        if ($entitlement->isEmpty()) {
            throw MissingEntitlement::create($entitlementId);
        }
        $viewer->get()->addEntitlement($entitlement->get());
        $this->viewers->save($viewer->get());
    }
}
