<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Infrastructure\Repository;

use App\Module\Entitlement\Domain\Viewer;
use App\Module\Entitlement\Domain\Viewers;
use Munus\Collection\Map;
use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

class InMemoryViewersRepository implements Viewers
{
    /** @var Map<Viewer> */
    private Map $viewers;

    public function __construct()
    {
        $this->viewers = Map::empty();
    }

    public function save(Viewer $viewer): void
    {
        $this->viewers = $this->viewers->put($viewer->getId()->toString(), $viewer);
    }

    public function findOneById(UuidInterface $viewerId): Option
    {
        return $this->viewers->get($viewerId->toString());
    }
}