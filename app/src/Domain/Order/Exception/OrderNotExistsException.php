<?php

namespace App\Domain\Order\Exception;

use RuntimeException;

final class OrderNotExistsException extends RuntimeException
{
    public function __construct(string $partnerId, string $externalOrderId)
    {
        parent::__construct("Order does not exists for partner '$partnerId' with ID '$externalOrderId'.");
    }
}
