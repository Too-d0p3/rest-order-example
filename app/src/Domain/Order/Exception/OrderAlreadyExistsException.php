<?php

namespace App\Domain\Order\Exception;

use RuntimeException;

final class OrderAlreadyExistsException extends RuntimeException
{
    public function __construct(string $partnerId, string $externalOrderId)
    {
        parent::__construct("Order already exists for partner '$partnerId' with ID '$externalOrderId'.");
    }
}
