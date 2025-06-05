<?php

namespace App\Domain\Order\Repository;

use App\Domain\Order\Entity\OrderProduct;

interface OrderProductRepository
{
    public function save(OrderProduct $product): void;
}
