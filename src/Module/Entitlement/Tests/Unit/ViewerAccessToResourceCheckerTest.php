<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Unit;

use App\Module\Entitlement\Application\UseCase\ViewerAccessToResourceChecker;
use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Exception\MissingResource;
use App\Module\Entitlement\Domain\Exception\MissingViewer;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Domain\Viewer;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryResourcesRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryViewersRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ViewerAccessToResourceCheckerTest extends TestCase
{
    private InMemoryViewersRepository $viewerRepository;
    private InMemoryResourcesRepository $resourceRepository;
    private ViewerAccessToResourceChecker $viewerAccessToResourceChecker;

    public function testWhenViewerCanAccessResource(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $entitlement->addResource($resource);
        $viewer->addEntitlement($entitlement);
        $this->resourceRepository->save($resource);
        $this->viewerRepository->save($viewer);

        self::assertTrue($this->viewerAccessToResourceChecker->canAccess($viewer->getId(), $resource->getId()));
    }

    public function testWhenViewerCantAccessResource(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $entitlement->addResource($resource);
        $this->resourceRepository->save($resource);
        $this->viewerRepository->save($viewer);

        self::assertFalse($this->viewerAccessToResourceChecker->canAccess($viewer->getId(), $resource->getId()));
    }

    public function testWhenViewerIsMissing(): void
    {
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $entitlement->addResource($resource);
        $this->resourceRepository->save($resource);

        $this->expectException(MissingViewer::class);

        $this->viewerAccessToResourceChecker->canAccess(Uuid::uuid4(), $resource->getId());
    }

    public function testWhenResourceIsMissing(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $this->viewerRepository->save($viewer);

        $this->expectException(MissingResource::class);

        $this->viewerAccessToResourceChecker->canAccess($viewer->getId(), Uuid::uuid4());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewerRepository = new InMemoryViewersRepository();
        $this->resourceRepository = new InMemoryResourcesRepository();
        $this->viewerAccessToResourceChecker = new ViewerAccessToResourceChecker($this->viewerRepository, $this->resourceRepository);
    }
}
