<?php

namespace App\Domain\Order\Command;

use App\Domain\Order\DTO\CreateOrderRequest;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Entity\OrderProduct;
use App\Domain\Order\Exception\InvalidDeliveryDateException;
use App\Domain\Order\Exception\OrderAlreadyExistsException;
use App\Domain\Order\Repository\OrderRepository;

readonly class CreateOrderHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function handle(CreateOrderRequest $request): Order
    {
        if ($this->orderRepository->findByPartnerAndOrderId($request->partnerId, $request->externalOrderId)) {
            throw new OrderAlreadyExistsException($request->partnerId, $request->externalOrderId);
        }

        try{
            $deliveryDate = new \DateTimeImmutable($request->deliveryDate);
        }catch (\Exception $e){
            throw new InvalidDeliveryDateException($request->deliveryDate);
        }


        $order = Order::create(
            partnerId: $request->partnerId,
            externalOrderId: $request->externalOrderId,
            deliveryDate: $deliveryDate,
            totalValue: (float) $request->totalValue
        );

        foreach ($request->products as $productData) {
            $order->addProduct(OrderProduct::create(
                productId: $productData->productId,
                name: $productData->name,
                price: (float) $productData->price,
                quantity: $productData->quantity
            ));
        }

        $this->orderRepository->save($order);

        return $order;
    }
}
