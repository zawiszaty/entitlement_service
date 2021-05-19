<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Integration;

use App\Module\Entitlement\Domain\Entitlement;
use App\Module\Entitlement\Domain\Resource;
use App\Module\Entitlement\Domain\Viewer;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryEntitlementsRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryResourcesRepository;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryViewersRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ResourcesControllerTest extends WebTestCase
{
    private Router $router;
    private InMemoryEntitlementsRepository $inMemoryEntitlementsRepository;
    private InMemoryResourcesRepository $inMemoryResourcesRepository;
    private InMemoryViewersRepository $inMemoryViewersRepository;
    private KernelBrowser $client;

    public function testWhenResourceIsCreated(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $viewer->addEntitlement($entitlement);
        $this->inMemoryViewersRepository->save($viewer);
        $this->inMemoryEntitlementsRepository->save($entitlement);

        $this->client->request('POST', $this->router->generate('create_resource'), [
            'name' => 'test',
            'category' => 'test',
            'entitlementsIds' => [
                $entitlement->getId()->toString(),
            ],
        ]);

        self::assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        /** @var resource $resource */
        $resource = $this->inMemoryResourcesRepository->findOneById(Uuid::fromString($response['id']))->get();
        self::assertCount(1, $resource->getEntitlements());
    }

    public function testWhenUserDontHaveAccessToResource(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $viewer->addEntitlement($entitlement);
        $this->inMemoryResourcesRepository->save($resource);
        $this->inMemoryViewersRepository->save($viewer);

        $this->client->request('GET', $this->router->generate('check_viewer_access_to_resource', ['resourceId' => $resource->getId()->toString(), 'viewersId' => $viewer->getId()->toString()]));

        self::assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testWhenEntitlementIsAssignToResource(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $viewer->addEntitlement($entitlement);
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $this->inMemoryViewersRepository->save($viewer);
        $this->inMemoryEntitlementsRepository->save($entitlement);
        $this->inMemoryResourcesRepository->save($resource);

        $this->client->request('PUT', $this->router->generate('assign_entitlement_to_resource', ['resourceId' => $resource->getId()->toString()]), [
            'entitlementsIds' => [
                $entitlement->getId()->toString(),
            ],
        ]);

        self::assertResponseIsSuccessful();
        /** @var resource $resource */
        $resource = $this->inMemoryResourcesRepository->findOneById($resource->getId())->get();
        self::assertCount(1, $resource->getEntitlements());
    }

    public function testWhenEntitlementIsAssignToResourceAndResourceNotExist(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $viewer->addEntitlement($entitlement);
        $this->inMemoryViewersRepository->save($viewer);
        $this->inMemoryEntitlementsRepository->save($entitlement);

        $this->client->request('PUT', $this->router->generate('assign_entitlement_to_resource', ['resourceId' => Uuid::uuid4()]), [
            'entitlementsIds' => [
                $entitlement->getId()->toString(),
            ],
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->router = self::$container->get('router');
        $this->inMemoryEntitlementsRepository = self::$container->get(InMemoryEntitlementsRepository::class);
        $this->inMemoryResourcesRepository = self::$container->get(InMemoryResourcesRepository::class);
        $this->inMemoryViewersRepository = self::$container->get(InMemoryViewersRepository::class);
    }
}
