<?php

declare(strict_types=1);


namespace App\Module\Entitlement\Domain;


use Munus\Control\Option;
use Ramsey\Uuid\UuidInterface;

interface Viewers
{
    public function save(Viewer $viewer): void;

    public function findOneById(UuidInterface $viewerId): Option;
}