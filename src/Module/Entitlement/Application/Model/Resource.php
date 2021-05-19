<?php

declare(strict_types=1);

namespace App\Module\Entitlement\Application\Model;

use Munus\Collection\GenericList;
use Ramsey\Uuid\UuidInterface;

class Resource implements \JsonSerializable
{
    private UuidInterface $id;

    private string $name;

    private string $category;

    /** @var GenericList<int> */
    private GenericList $entitlements;

    /**
     * @param GenericList<int> $entitlements
     */
    public function __construct(UuidInterface $id, string $name, string $category, GenericList $entitlements)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->entitlements = $entitlements;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return GenericList<int>
     */
    public function getEntitlements(): GenericList
    {
        return $this->entitlements;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'category' => $this->category,
            'entitlements' => $this->entitlements->map(function (UuidInterface $id) {
                return $id->toString();
            })->toArray(),
        ];
    }
}
