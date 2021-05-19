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

class ViewersControllerTest extends WebTestCase
{
    private Router $router;
    private InMemoryEntitlementsRepository $inMemoryEntitlementsRepository;
    private InMemoryResourcesRepository $inMemoryResourcesRepository;
    private InMemoryViewersRepository $inMemoryViewersRepository;
    private KernelBrowser $client;

    public function testWhenUserHaveAccessToResource(): void
    {
        $viewer = new Viewer(Uuid::uuid4());
        $entitlement = new Entitlement(Uuid::uuid4());
        $resource = new Resource(Uuid::uuid4(), 'test', 'test');
        $entitlement->addResource($resource);
        $viewer->addEntitlement($entitlement);
        $this->inMemoryResourcesRepository->save($resource);
        $this->inMemoryViewersRepository->save($viewer);

        $this->client->request('GET', $this->router->generate('check_viewer_access_to_resource', ['resourceId' => $resource->getId()->toString(), 'viewersId' => $viewer->getId()->toString()]));

        self::assertResponseIsSuccessful();
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
