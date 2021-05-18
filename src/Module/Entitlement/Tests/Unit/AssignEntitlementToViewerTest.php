<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Unit;

use App\Module\Entitlement\Application\UseCase\AssignEntitlementToViewer;
use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Exception\MissingViewer;
use App\Module\Entitlement\Domain\Viewer;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryEntitlementsRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryViewersRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AssignEntitlementToViewerTest extends TestCase
{
    private InMemoryViewersRepository $viewerRepository;
    private InMemoryEntitlementsRepository $entitlementRepository;
    private AssignEntitlementToViewer $assignEntitlementToViewer;

    public function testWhenAssignEntitlementToViewer(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $this->viewerRepository->save($viewer);
        $this->entitlementRepository->save($entitlement);

        $this->assignEntitlementToViewer->assign($viewer->getId(), $entitlement->getId());

        /** @var Viewer $viewer */
        $viewer = $this->viewerRepository->findOneById($viewer->getId())->get();
        self::assertSame($entitlement->getId()->toString(), $viewer->getEntitlements()->getIterator()->current()->getId()->toString());
    }

    public function testWhenViewerIsMissing(): void
    {
        $entitlement = new Entitlement(Uuid::uuid4());
        $this->entitlementRepository->save($entitlement);

        $this->expectException(MissingViewer::class);

        $this->assignEntitlementToViewer->assign(Uuid::uuid4(), $entitlement->getId());
    }

    public function testWhenEntitlementIsMissing(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $this->viewerRepository->save($viewer);

        $this->expectException(MissingEntitlement::class);

        $this->assignEntitlementToViewer->assign($viewer->getId(), Uuid::uuid4());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewerRepository = new InMemoryViewersRepository();
        $this->entitlementRepository = new InMemoryEntitlementsRepository();
        $this->assignEntitlementToViewer = new AssignEntitlementToViewer($this->viewerRepository, $this->entitlementRepository);
    }
}
