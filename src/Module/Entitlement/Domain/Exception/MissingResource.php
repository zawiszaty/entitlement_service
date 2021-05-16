<?php

declare(strict_types=1);


namespace App\Module\Entitlement\Domain\Exception;


use Ramsey\Uuid\UuidInterface;

class MissingResource extends \RuntimeException
{
    public static function create(UuidInterface $id): self
    {
        return new self(sprintf('Resource with id: %s is missing', $id->toString()));
    }
}