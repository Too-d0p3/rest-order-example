<?php

namespace App\Domain\Order\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'order_products')]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['order:read'])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['order:read'])]
    private string $productId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['order:read'])]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['order:read'])]
    private float $price;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $quantity;

    private function __construct(
        Uuid $id,
        string $productId,
        string $name,
        float $price,
        int $quantity
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public static function create(
        string $productId,
        string $name,
        float $price,
        int $quantity
    ): self {
        return new self(
            Uuid::v4(),
            $productId,
            $name,
            $price,
            $quantity
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
