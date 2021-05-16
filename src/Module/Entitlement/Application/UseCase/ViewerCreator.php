<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\UseCase;

use App\Module\Entitlement\Domain\Viewer;
use App\Module\Entitlement\Domain\Viewers;
use Ramsey\Uuid\UuidInterface;

class ViewerCreator
{
    private Viewers $viewers;

    public function __construct(Viewers $viewers)
    {
        $this->viewers = $viewers;
    }

    public function create(UuidInterface $id): void
    {
        $viewer = new Viewer(
            $id
        );
        $this->viewers->save($viewer);
    }
}