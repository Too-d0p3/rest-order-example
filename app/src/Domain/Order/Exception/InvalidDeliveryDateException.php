<?php

namespace App\Domain\Order\Exception;

use RuntimeException;

final class InvalidDeliveryDateException extends RuntimeException
{
    public function __construct(string $date)
    {
        parent::__construct("Invalid delivery date: '$date'");
    }
}
