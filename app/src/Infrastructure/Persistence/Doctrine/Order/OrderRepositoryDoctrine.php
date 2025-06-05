<?php

namespace App\Infrastructure\Persistence\Doctrine\Order;

use App\Domain\Order\Entity\Order;
use App\Domain\Order\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepositoryDoctrine implements OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
    }

    public function findByPartnerAndOrderId(string $partnerId, string $orderId): ?Order
    {
        return $this->em->getRepository(Order::class)
            ->findOneBy(['partnerId' => $partnerId, 'externalOrderId' => $orderId]);
    }
}
