<?php

namespace App\Domain\Order\DTO;

use App\Shared\DTO\Dto;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateOrderRequest implements Dto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $partnerId,

        #[Assert\NotBlank]
        public string $externalOrderId,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $deliveryDate,

        #[Assert\NotBlank]
        #[Assert\Type('float')]
        #[Assert\GreaterThanOrEqual(0)]
        public ?float $totalValue,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $products // @var OrderProductData[]
    ) {}

    public static function fromArray(array $data): static
    {
        $products = array_map(
            fn(array $item) => OrderProductData::fromArray($item),
            $data['products'] ?? []
        );

        return new self(
            partnerId: $data['partnerId'] ?? '',
            externalOrderId: $data['externalOrderId'] ?? '',
            deliveryDate: $deliveryDate = $data['deliveryDate'] ?? '',
            totalValue: is_numeric($data['totalValue'] ?? '') ? (float) $data['totalValue'] : null,
            products: $products
        );
    }
}
