<?php

namespace App\Tests\Functional\Order;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderEndpointTest extends FunctionalTestCase
{
    public function testSuccessfulOrderCreation(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-123',
            'externalOrderId' => 'order-456',
            'deliveryDate' => '2025-06-10',
            'totalValue' => '999.99',
            'products' => [
                [
                    'productId' => 'p1',
                    'name' => 'Product 1',
                    'price' => 100.5,
                    'quantity' => 2,
                ],
                [
                    'productId' => 'p2',
                    'name' => 'Product 2',
                    'price' => 50.0,
                    'quantity' => 3,
                ]
            ]
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('partner-123', $responseData['partnerId']);
        $this->assertEquals('order-456', $responseData['externalOrderId']);
    }

    public function testDuplicateOrder(): void
    {
        $data = [
            'partnerId' => 'partner-dup',
            'externalOrderId' => 'order-dup',
            'deliveryDate' => '2025-06-10',
            'totalValue' => '100.00',
            'products' => [
                [
                    'productId' => 'p1',
                    'name' => 'Product 1',
                    'price' => 50.0,
                    'quantity' => 2,
                ]
            ]
        ];

        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // try again with same data -> conflict expected
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('Order Already Exists', $responseData['title']);
    }

    public function testInvalidDeliveryDate(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-xyz',
            'externalOrderId' => 'order-xyz',
            'deliveryDate' => 'invalid-date',
            'totalValue' => '500.00',
            'products' => [
                [
                    'productId' => 'p1',
                    'name' => 'Product 1',
                    'price' => 50.0,
                    'quantity' => 2,
                ]
            ]
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('Invalid Input', $responseData['title']);
    }

    public function testValidationErrors(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => '',
            'externalOrderId' => '',
            'deliveryDate' => '',
            'totalValue' => '-5',
            'products' => []
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertArrayHasKey('invalid-params', $responseData);
    }

    public function testMissingRequiredFields(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-123'
            // missing other required fields
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('Validation Failed', $responseData['title']);
        $this->assertArrayHasKey('invalid-params', $responseData);
    }

    public function testInvalidProductData(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-123',
            'externalOrderId' => 'order-456',
            'deliveryDate' => '2025-06-10',
            'totalValue' => '100.00',
            'products' => [
                [
                    'productId' => 'p1',
                    'name' => 'Product 1',
                    'price' => -50.0, // invalid negative price
                    'quantity' => 0, // invalid quantity
                ]
            ]
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertArrayHasKey('invalid-params', $responseData);
    }

    public function testEmptyProductsList(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-123',
            'externalOrderId' => 'order-456',
            'deliveryDate' => '2025-06-10',
            'totalValue' => '100.00',
            'products' => [] // empty products list
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertArrayHasKey('invalid-params', $responseData);
    }

    public function testInvalidTotalValue(): void
    {
        $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'partnerId' => 'partner-123',
            'externalOrderId' => 'order-456',
            'deliveryDate' => '2025-06-10',
            'totalValue' => 'invalid-value',
            'products' => [
                [
                    'productId' => 'p1',
                    'name' => 'Product 1',
                    'price' => 50.0,
                    'quantity' => 2,
                ]
            ]
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('application/problem+json', $this->client->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('Validation Failed', $responseData['title']);
        $this->assertArrayHasKey('invalid-params', $responseData);
    }

    public function testVariousPartnerAndOrderIds(): void
    {
        $testCases = [
            [
                'partnerId' => '123',
                'externalOrderId' => '456',
                'description' => 'numeric IDs'
            ],
            [
                'partnerId' => 'partner-123',
                'externalOrderId' => 'order-456',
                'description' => 'alphanumeric IDs'
            ],
            [
                'partnerId' => 'PARTNER_123',
                'externalOrderId' => 'ORDER_456',
                'description' => 'uppercase with underscore'
            ],
            [
                'partnerId' => 'partner.123',
                'externalOrderId' => 'order.456',
                'description' => 'with dots'
            ]
        ];

        foreach ($testCases as $testCase) {
            $this->client->request('POST', '/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'partnerId' => $testCase['partnerId'],
                'externalOrderId' => $testCase['externalOrderId'],
                'deliveryDate' => '2025-06-10',
                'totalValue' => '100.00',
                'products' => [
                    [
                        'productId' => 'p1',
                        'name' => 'Product 1',
                        'price' => 50.0,
                        'quantity' => 2,
                    ]
                ]
            ]));

            $this->assertResponseStatusCodeSame(Response::HTTP_CREATED, "Failed for {$testCase['description']}");
            $responseData = json_decode($this->client->getResponse()->getContent(), true);
            $this->assertEquals($testCase['partnerId'], $responseData['partnerId']);
            $this->assertEquals($testCase['externalOrderId'], $responseData['externalOrderId']);
        }
    }
}