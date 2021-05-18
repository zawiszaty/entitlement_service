<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Tests\Unit;

use App\Module\Entitlement\Application\UseCase\ViewerCreator;
use App\Module\Entitlement\Infrastructure\Repository\InMemoryViewersRepository;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ViewerCreatorTest extends TestCase
{
    private ViewerCreator $viewerCreator;
    private InMemoryViewersRepository $repository;

    public function testWhenViewerIsCreated(): void
    {
        $id = Uuid::uuid4();

        $this->viewerCreator->create($id);

        self::assertFalse($this->repository->findOneById($id)->isEmpty());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new InMemoryViewersRepository();
        $this->viewerCreator = new ViewerCreator($this->repository);
    }
}
