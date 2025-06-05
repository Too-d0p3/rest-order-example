<?php

namespace App\Infrastructure\Persistence\Doctrine\Order;

use App\Domain\Order\Entity\OrderProduct;
use App\Domain\Order\Repository\OrderProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderProductRepositoryDoctrine implements OrderProductRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(OrderProduct $product): void
    {
        $this->em->persist($product);
        $this->em->flush();
    }
}
