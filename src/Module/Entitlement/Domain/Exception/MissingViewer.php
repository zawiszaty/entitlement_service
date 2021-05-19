<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain\Exception;

use App\Infrastructure\Exception\NotFoundException;
use Ramsey\Uuid\UuidInterface;

class MissingViewer extends NotFoundException
{
    public static function create(UuidInterface $id): self
    {
        return new self(sprintf('Viewer with id: %s is missing', $id->toString()));
    }
}
