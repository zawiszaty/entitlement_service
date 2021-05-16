<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Unit;

use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Domain\Viewer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ViewerTest extends TestCase
{
    public function testWhenViewerTryAccessResourceWithActivePlan(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test ppv', 'sport');
        $entitlement = new Entitlement(Uuid::uuid4());
        $entitlement->addViewer($viewer);
        $entitlement->addResource($resource);

        $canAccess = $viewer->canAccessResource($resource);

        self::assertTrue($canAccess);
    }

    public function testWhenViewerTryAccessBronzeResourceWithActiveGoldPlan(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test ppv', 'sport');
        $bronzeEntitlement = new Entitlement(Uuid::uuid4());
        $bronzeEntitlement->addResource($resource);
        $silverEntitlement = new Entitlement(Uuid::uuid4());
        $silverEntitlement->addChildrenEntitlement($bronzeEntitlement);
        $goldEntitlement = new Entitlement(Uuid::uuid4());
        $goldEntitlement->addChildrenEntitlement($silverEntitlement);
        $goldEntitlement->addViewer($viewer);

        $canAccess = $viewer->canAccessResource($resource);

        self::assertTrue($canAccess);
    }

    public function testWhenViewerTryAccessResourceWithExpiredPlan(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test ppv', 'sport');
        $expiredAt = (new \DateTime())->modify('-1 days');
        $entitlement = new Entitlement(Uuid::uuid4(), $expiredAt);
        $entitlement->addViewer($viewer);
        $entitlement->addResource($resource);

        $canAccess = $viewer->canAccessResource($resource);

        self::assertFalse($canAccess);
    }

    public function testWhenViewerTryAccessResourceWithoutActivePlan(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test ppv', 'sport');
        $entitlement = new Entitlement(Uuid::uuid4());
        $entitlement->addViewer($viewer);
        $entitlement->addResource($resource);

        $canAccess = $viewer->canAccessResource(new Resource(Uuid::uuid4(), 'funny ppv', 'funny'));

        self::assertFalse($canAccess);
    }
}