<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Domain\Exception;

use App\Infrastructure\Exception\NotFoundException;
use Ramsey\Uuid\UuidInterface;

final class MissingEntitlement extends NotFoundException
{
    public static function create(UuidInterface $id): self
    {
        return new self(sprintf('Entitlement with id: %s is missing', $id->toString()));
    }
}
