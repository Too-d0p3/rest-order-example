<?php

namespace App\Domain\Order\Command;

use App\Domain\Order\DTO\UpdateDeliveryDateRequest;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Exception\InvalidDeliveryDateException;
use App\Domain\Order\Exception\OrderNotExistsException;
use App\Domain\Order\Repository\OrderRepository;

readonly class UpdateDeliveryDateHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function handle(UpdateDeliveryDateRequest $request): Order
    {
        $order = $this->orderRepository->findByPartnerAndOrderId($request->partnerId, $request->externalOrderId);
        if (!$order) {
            throw new OrderNotExistsException($request->partnerId, $request->externalOrderId);
        }

        try{
            $deliveryDate = new \DateTimeImmutable($request->deliveryDate);
        }catch (\Exception $e){
            throw new InvalidDeliveryDateException($request->deliveryDate);
        }

        $order->updateDeliveryDate($deliveryDate);

        $this->orderRepository->save($order);

        return $order;
    }
}