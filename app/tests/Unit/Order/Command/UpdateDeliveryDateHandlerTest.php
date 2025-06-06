<?php

namespace App\Tests\Unit\Order\Command;

use App\Domain\Order\Command\UpdateDeliveryDateHandler;
use App\Domain\Order\DTO\UpdateDeliveryDateRequest;
use App\Domain\Order\Entity\Order;
use App\Domain\Order\Exception\InvalidDeliveryDateException;
use App\Domain\Order\Exception\OrderNotExistsException;
use App\Domain\Order\Repository\OrderRepository;
use PHPUnit\Framework\TestCase;

class UpdateDeliveryDateHandlerTest extends TestCase
{
    public function testSuccessfulUpdate()
    {
        $order = $this->createMock(Order::class);
        $order->expects($this->once())
            ->method('updateDeliveryDate')
            ->with($this->isInstanceOf(\DateTimeImmutable::class));

        $repository = $this->createMock(OrderRepository::class);
        $repository->method('findByPartnerAndOrderId')->willReturn($order);
        $repository->expects($this->once())->method('save')->with($order);

        $handler = new UpdateDeliveryDateHandler($repository);

        $request = new UpdateDeliveryDateRequest(
            partnerId: 'partner-123',
            externalOrderId: 'order-456',
            deliveryDate: '2025-06-10'
        );

        $result = $handler->handle($request);
        $this->assertSame($order, $result);
    }

    public function testThrowsWhenOrderNotFound()
    {
        $this->expectException(OrderNotExistsException::class);

        $repository = $this->createMock(OrderRepository::class);
        $repository->method('findByPartnerAndOrderId')->willReturn(null);
        $repository->expects($this->never())->method('save');

        $handler = new UpdateDeliveryDateHandler($repository);

        $request = new UpdateDeliveryDateRequest(
            partnerId: 'non-existent-partner',
            externalOrderId: 'non-existent-order',
            deliveryDate: '2025-06-10'
        );

        $handler->handle($request);
    }
}
