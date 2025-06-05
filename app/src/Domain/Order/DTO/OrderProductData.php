<?php

namespace App\Domain\Order\DTO;

use App\Shared\DTO\Dto;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderProductData implements Dto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $productId,

        #[Assert\NotBlank]
        public string $name,

        #[Assert\GreaterThanOrEqual(0)]
        public float $price,

        #[Assert\GreaterThan(0)]
        public int $quantity
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            productId: $data['productId'] ?? '',
            name: $data['name'] ?? '',
            price: (float) ($data['price'] ?? 0),
            quantity: (int) ($data['quantity'] ?? 0),
        );
    }
}
