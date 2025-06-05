<?php

namespace App\Domain\Order\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(columns: ['partner_id', 'external_order_id'])]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['order:read', 'order:list'])]
    private Uuid $id;

    #[ORM\Column(name: 'partner_id', type: 'string', length: 255)]
    #[Groups(['order:read', 'order:list'])]
    private string $partnerId;

    #[ORM\Column(name: 'external_order_id', type: 'string', length: 255)]
    #[Groups(['order:read', 'order:list'])]
    private string $externalOrderId;

    #[ORM\Column(name: 'delivery_date', type: 'datetime_immutable')]
    #[Groups(['order:read', 'order:list'])]
    private \DateTimeImmutable $deliveryDate;

    #[ORM\Column(name: 'total_value', type: 'float')]
    #[Groups(['order:read', 'order:list'])]
    private float $totalValue;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['order:read'])]
    private Collection $products;

    private function __construct(
        Uuid $id,
        string $partnerId,
        string $externalOrderId,
        \DateTimeImmutable $deliveryDate,
        float $totalValue
    ) {
        $this->id = $id;
        $this->partnerId = $partnerId;
        $this->externalOrderId = $externalOrderId;
        $this->deliveryDate = $deliveryDate;
        $this->totalValue = $totalValue;
        $this->products = new ArrayCollection();
    }

    public static function create(
        string $partnerId,
        string $externalOrderId,
        \DateTimeImmutable $deliveryDate,
        float $totalValue
    ): self {
        return new self(
            Uuid::v4(),
            $partnerId,
            $externalOrderId,
            $deliveryDate,
            $totalValue
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPartnerId(): string
    {
        return $this->partnerId;
    }

    public function getExternalOrderId(): string
    {
        return $this->externalOrderId;
    }

    public function getDeliveryDate(): \DateTimeImmutable
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeImmutable $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function getTotalValue(): float
    {
        return $this->totalValue;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(OrderProduct $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrder($this);
        }
    }

    public function removeProduct(OrderProduct $product): void
    {
        if ($this->products->removeElement($product)) {
            $product->setOrder(null);
        }
    }
}
