<?php

declare(strict_types=1);


namespace App\Module\Entitlement\Tests\Unit;


use App\Module\Entitlement\Application\UseCase\ResourceCreator;
use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Exception\MissingEntitlement;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryEntitlementsRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryResourcesRepository;
use Munus\Collection\GenericList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;

class ResourceCreatorTest extends TestCase
{
    private InMemoryResourcesRepository $repository;
    private ResourceCreator $creator;
    private InMemoryEntitlementsRepository $entitlementRepository;

    public function testWhenResourceIsCreated(): void
    {
        $this->creator->create('test', 'test', GenericList::empty());

        $resource = $this->repository->getAll()->getIterator()->current();
        self::assertInstanceOf(Resource::class, $resource);
    }

    public function testWhenEntitlementIsMissing(): void
    {
        $this->expectException(MissingEntitlement::class);

        $this->creator->create('test', 'test', GenericList::of(Uuid::uuid4()));
    }

    public function testWhenResourceIsCreatedWithEntitlement(): void
    {
        $entitlement = new Entitlement(Uuid::uuid4());
        $this->entitlementRepository->save($entitlement);
        $entitlementsIds = GenericList::of($entitlement->getId());
        $this->creator->create('test', 'test', $entitlementsIds);

        /** @var Resource $resource */
        $resource = $this->repository->getAll()->getIterator()->current();
        self::assertInstanceOf(Resource::class, $resource);
        self::assertCount(1, $resource->getEntitlements());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryResourcesRepository();
        $this->entitlementRepository = new InMemoryEntitlementsRepository();
        $this->creator = new ResourceCreator($this->repository, $this->entitlementRepository);
    }
}