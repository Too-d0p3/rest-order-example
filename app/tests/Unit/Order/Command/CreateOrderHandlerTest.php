<?php

namespace App\Tests\Unit\Order\Command;

use App\Domain\Order\Command\CreateOrderHandler;
use App\Domain\Order\DTO\CreateOrderRequest;
use App\Domain\Order\DTO\OrderProductData;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Exception\OrderAlreadyExistsException;
use App\Domain\Order\Repository\OrderRepository;
use PHPUnit\Framework\TestCase;

class CreateOrderHandlerTest extends TestCase
{
    public function testHandleCreatesNewOrder(): void
    {
        $repository = $this->createMock(OrderRepository::class);
        $repository->method('findByPartnerAndOrderId')->willReturn(null);
        $repository->expects($this->once())->method('save');

        $handler = new CreateOrderHandler($repository);

        $request = new CreateOrderRequest(
            partnerId: 'partner-1',
            externalOrderId: 'order-123',
            deliveryDate: '2025-06-10',
            totalValue: '199.99',
            products: [
                new OrderProductData('p1', 'Test Product', '99.99', 2)
            ]
        );

        $order = $handler->handle($request);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('partner-1', $order->getPartnerId());
        $this->assertCount(1, $order->getProducts());
    }

    public function testHandleThrowsOnDuplicateOrder(): void
    {
        $order = $this->createMock(Order::class);
        $this->expectException(OrderAlreadyExistsException::class);

        $repository = $this->createMock(OrderRepository::class);
        $repository->method('findByPartnerAndOrderId')->willReturn($order);
        $repository->expects($this->never())->method('save');

        $handler = new CreateOrderHandler($repository);

        $request = new CreateOrderRequest(
            partnerId: 'partner-1',
            externalOrderId: 'order-123',
            deliveryDate: '2025-06-10',
            totalValue: '199.99',
            products: [
                new OrderProductData('p1', 'Test Product', '99.99', 2)
            ]
        );

        $handler->handle($request);
    }
}
