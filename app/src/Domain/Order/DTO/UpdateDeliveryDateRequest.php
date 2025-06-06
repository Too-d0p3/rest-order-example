<?php

namespace App\Domain\Order\DTO;

use App\Domain\Order\Exception\InvalidDeliveryDateException;
use App\Shared\DTO\Dto;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateDeliveryDateRequest implements Dto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $partnerId,

        #[Assert\NotBlank]
        public string $externalOrderId,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $deliveryDate,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            partnerId: $data['partnerId'] ?? '',
            externalOrderId: $data['externalOrderId'] ?? '',
            deliveryDate: $data['deliveryDate'] ?? '',
        );
    }
}