<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Unit;

use App\Module\Entitlement\Application\UseCase\AssignResourceToEntitlement;
use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Exception\MissingResource;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryEntitlementsRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryResourcesRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AssignResourceToEntitlementTest extends TestCase
{
    private InMemoryEntitlementsRepository $entitlementRepository;
    private InMemoryResourcesRepository $resourceRepository;
    private AssignResourceToEntitlement $assignResourceToEntitlement;

    public function testWhenAssignResourceToEntitlement(): void
    {
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $this->entitlementRepository->save($entitlement);
        $this->resourceRepository->save($resource);

        $this->assignResourceToEntitlement->assign($entitlement->getId(), $resource->getId());

        /** @var Entitlement $entitlement */
        $entitlement = $this->entitlementRepository->findOneById($entitlement->getId())->get();
        self::assertSame($resource->getId()->toString(), $entitlement->getResources()->getIterator()->current()->getId()->toString());
    }

    public function testWhenEntitlementIsMissing(): void
    {
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $this->resourceRepository->save($resource);

        $this->expectException(MissingEntitlement::class);

        $this->assignResourceToEntitlement->assign(Uuid::uuid4(), $resource->getId());
    }

    public function testWhenResourceIsMissing(): void
    {
        $entitlement = new Entitlement(Uuid::uuid4());
        $this->entitlementRepository->save($entitlement);

        $this->expectException(MissingResource::class);

        $this->assignResourceToEntitlement->assign($entitlement->getId(), Uuid::uuid4());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->entitlementRepository = new InMemoryEntitlementsRepository();
        $this->resourceRepository = new InMemoryResourcesRepository();
        $this->assignResourceToEntitlement = new AssignResourceToEntitlement($this->entitlementRepository, $this->resourceRepository);
    }
}